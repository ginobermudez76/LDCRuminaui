import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TableModule } from 'primeng/table';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { SelectModule } from 'primeng/select';
import { MessageModule } from 'primeng/message';
import { ConfirmDialogModule } from 'primeng/confirmdialog';
import { ConfirmationService } from 'primeng/api';
import { PublicistaService, DeportistaDestacado, Deporte } from '../../../core/services/publicista.service';

@Component({
  selector: 'app-deportistas',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TableModule, DialogModule, ButtonModule, InputTextModule, SelectModule, MessageModule, ConfirmDialogModule],
  providers: [ConfirmationService],
  template: `
    <div class="pub-container">
      <header class="page-header blue">
        <h1>🌟 Deportistas Destacados</h1>
        <p>Gestiona el reconocimiento de nuestros mejores deportistas.</p>
      </header>
      <div class="table-card">
        <p-table [value]="items()" [paginator]="true" [rows]="10" styleClass="p-datatable-sm p-datatable-striped" responsiveLayout="scroll">
          <ng-template pTemplate="caption">
            <div class="table-caption">
              <span class="table-title">Deportistas Destacados</span>
              <button pButton type="button" icon="pi pi-plus" label="Nuevo Deportista" class="p-button-sm" (click)="openDialog()"></button>
            </div>
          </ng-template>
          <ng-template pTemplate="header">
            <tr><th>ID</th><th>Foto</th><th>Nombre</th><th>Deporte</th><th>Acciones</th></tr>
          </ng-template>
          <ng-template pTemplate="body" let-item>
            <tr>
              <td>{{ item.id }}</td>
              <td><img *ngIf="item.imagen" [src]="'http://localhost:8000' + item.imagen" class="thumb round" /></td>
              <td><strong>{{ item.nombre_deportista }}</strong></td>
              <td>{{ item.deporte?.nombre }}</td>
              <td><button pButton icon="pi pi-trash" class="p-button-danger p-button-sm p-button-text" (click)="confirmDelete(item.id)"></button></td>
            </tr>
          </ng-template>
          <ng-template pTemplate="emptymessage"><tr><td colspan="5" class="empty-msg">No hay deportistas registrados.</td></tr></ng-template>
        </p-table>
      </div>
      <p-dialog [(visible)]="showDialog" header="Nuevo Deportista Destacado" [modal]="true" [style]="{width:'480px'}">
        <form [formGroup]="form" (ngSubmit)="onSubmit()" class="modal-form">
          <div class="field"><label>Nombre Completo *</label><input pInputText formControlName="nombre_deportista" /></div>
          <div class="field"><label>Deporte</label>
            <p-select [options]="deportes()" optionLabel="nombre" optionValue="id" formControlName="deporte_id" [style]="{width:'100%'}"></p-select>
          </div>
          <div class="field"><label>Foto</label><input type="file" accept="image/*" (change)="onFileChange($event)" class="file-input" /></div>
          <div class="error-wrap" *ngIf="error()"><p-message severity="error" [text]="error()!"></p-message></div>
          <div class="modal-footer">
            <button pButton type="button" label="Cancelar" class="p-button-text" (click)="showDialog=false"></button>
            <button pButton type="submit" label="Guardar" icon="pi pi-check" [disabled]="form.invalid" [loading]="saving()"></button>
          </div>
        </form>
      </p-dialog>
      <p-confirmDialog></p-confirmDialog>
    </div>
  `,
  styles: [`
    .pub-container { display: flex; flex-direction: column; gap: 20px; }
    .page-header { padding: 20px 24px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .page-header h1 { margin: 0 0 4px 0; font-size: 22px; font-weight: 700; }
    .page-header p { margin: 0; opacity: 0.9; font-size: 13px; }
    .blue { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); }
    .table-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    .table-caption { display: flex; justify-content: space-between; align-items: center; }
    .table-title { font-size: 16px; font-weight: 700; color: #1e293b; }
    .thumb { width: 48px; height: 48px; object-fit: cover; border: 2px solid #e2e8f0; }
    .thumb.round { border-radius: 50%; }
    .empty-msg { text-align: center; padding: 20px; color: #64748b; }
    .modal-form { display: flex; flex-direction: column; gap: 16px; margin-top: 12px; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-size: 13px; font-weight: 600; color: #475569; }
    .file-input { border: 1px dashed #cbd5e1; padding: 10px; border-radius: 6px; cursor: pointer; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
  `]
})
export class DeportistasComponent implements OnInit {
  private svc = inject(PublicistaService);
  private fb = inject(FormBuilder);
  private confirm = inject(ConfirmationService);
  items = signal<DeportistaDestacado[]>([]);
  deportes = signal<Deporte[]>([]);
  showDialog = false;
  saving = signal(false);
  error = signal<string | undefined>(undefined);
  form!: FormGroup;
  private selectedFile: File | null = null;

  ngOnInit() {
    this.form = this.fb.group({ nombre_deportista: ['', Validators.required], deporte_id: [null] });
    this.load();
    this.svc.getDeportes().subscribe(d => this.deportes.set(d));
  }

  load() { this.svc.getDeportistas().subscribe(d => this.items.set(d)); }
  onFileChange(e: any) { this.selectedFile = e.target.files[0] || null; }
  openDialog() { this.form.reset(); this.selectedFile = null; this.error.set(undefined); this.showDialog = true; }

  onSubmit() {
    if (this.form.invalid) return;
    this.saving.set(true);
    const fd = new FormData();
    fd.append('nombre_deportista', this.form.value.nombre_deportista);
    if (this.form.value.deporte_id) fd.append('deporte_id', String(this.form.value.deporte_id));
    if (this.selectedFile) fd.append('imagen', this.selectedFile);
    this.svc.createDeportista(fd).subscribe({ next: () => { this.saving.set(false); this.showDialog = false; this.load(); }, error: (e) => { this.saving.set(false); this.error.set(e.error?.error || 'Error al guardar'); }});
  }

  confirmDelete(id: number) {
    this.confirm.confirm({ message: '¿Eliminar este deportista?', accept: () => this.svc.deleteDeportista(id).subscribe(() => this.load()) });
  }
}
