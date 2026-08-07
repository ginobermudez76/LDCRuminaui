import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TableModule } from 'primeng/table';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { TextareaModule } from 'primeng/textarea';
import { MessageModule } from 'primeng/message';
import { ConfirmDialogModule } from 'primeng/confirmdialog';
import { ConfirmationService } from 'primeng/api';
import { PublicistaService, Deporte } from '../../../core/services/publicista.service';

@Component({
  selector: 'app-deportes',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    TableModule,
    DialogModule,
    ButtonModule,
    InputTextModule,
    TextareaModule,
    MessageModule,
    ConfirmDialogModule,
  ],
  providers: [ConfirmationService],
  templateUrl: './deportes.component.html',
  styles: [pubStyles()],
})
export class DeportesComponent implements OnInit {
  private readonly svc = inject(PublicistaService);
  private readonly fb = inject(FormBuilder);
  private readonly confirm = inject(ConfirmationService);
  items = signal<Deporte[]>([]);
  showDialog = false;
  saving = signal(false);
  error = signal<string | undefined>(undefined);
  form!: FormGroup;
  private selectedFile: File | null = null;

  ngOnInit() {
    this.form = this.fb.group({ nombre: ['', Validators.required], descripcion: [''] });
    this.load();
  }

  load() {
    this.svc.getDeportes().subscribe((d) => this.items.set(d));
  }

  onFileChange(e: any) {
    this.selectedFile = e.target.files[0] || null;
  }

  openDialog() {
    this.form.reset();
    this.selectedFile = null;
    this.error.set(undefined);
    this.showDialog = true;
  }

  onSubmit() {
    if (this.form.invalid) return;
    this.saving.set(true);
    const fd = new FormData();
    fd.append('nombre', this.form.value.nombre);
    if (this.form.value.descripcion) fd.append('descripcion', this.form.value.descripcion);
    if (this.selectedFile) fd.append('imagen', this.selectedFile);
    this.svc.createDeporte(fd).subscribe({
      next: () => {
        this.saving.set(false);
        this.showDialog = false;
        this.load();
      },
      error: (e) => {
        this.saving.set(false);
        this.error.set(e.error?.error || 'Error al guardar');
      },
    });
  }

  confirmDelete(uuid: string) {
    this.confirm.confirm({
      message: '¿Eliminar este registro?',
      accept: () => this.svc.deleteDeporte(uuid).subscribe(() => this.load()),
    });
  }
}

function pubStyles(): string {
  return `
    .pub-container { display: flex; flex-direction: column; gap: 20px; }
    .page-header { padding: 20px 24px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .page-header h1 { margin: 0 0 4px 0; font-size: 22px; font-weight: 700; }
    .page-header p { margin: 0; opacity: 0.9; font-size: 13px; }
    .indigo { background: linear-gradient(135deg, #3730a3 0%, #6366f1 100%); }
    .table-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    .table-caption { display: flex; justify-content: space-between; align-items: center; }
    .table-title { font-size: 16px; font-weight: 700; color: #1e293b; }
    .thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .empty-msg { text-align: center; padding: 20px; color: #64748b; }
    .modal-form { display: flex; flex-direction: column; gap: 16px; margin-top: 12px; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-size: 13px; font-weight: 600; color: #475569; }
    .file-input { border: 1px dashed #cbd5e1; padding: 10px; border-radius: 6px; cursor: pointer; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
  `;
}
