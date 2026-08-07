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
import { PublicistaService, Evento, Deporte } from '../../../core/services/publicista.service';

@Component({
  selector: 'app-eventos',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    TableModule,
    DialogModule,
    ButtonModule,
    InputTextModule,
    SelectModule,
    MessageModule,
    ConfirmDialogModule,
  ],
  providers: [ConfirmationService],
  templateUrl: './eventos.component.html',
  styles: [pubStyles()],
})
export class EventosComponent implements OnInit {
  private svc = inject(PublicistaService);
  private fb = inject(FormBuilder);
  private confirm = inject(ConfirmationService);
  items = signal<Evento[]>([]);
  deportes = signal<Deporte[]>([]);
  showDialog = false;
  saving = signal(false);
  error = signal<string | undefined>(undefined);
  form!: FormGroup;
  private selectedFile: File | null = null;

  ngOnInit() {
    this.form = this.fb.group({
      nombre: ['', Validators.required],
      fecha_inicio: ['', Validators.required],
      fecha_fin: ['', Validators.required],
      deporte_id: [null, Validators.required],
      descripcion: [''],
      inscripciones: ['Activo'],
    });
    this.load();
    this.svc.getDeportes().subscribe((d) => this.deportes.set(d));
  }

  load() {
    this.svc.getEventos().subscribe((d) => this.items.set(d));
  }

  onFileChange(e: any) {
    this.selectedFile = e.target.files[0] || null;
  }

  openDialog() {
    this.form.reset({ inscripciones: 'Activo' });
    this.selectedFile = null;
    this.error.set(undefined);
    this.showDialog = true;
  }

  onSubmit() {
    if (this.form.invalid) return;
    this.saving.set(true);
    const fd = new FormData();
    Object.entries(this.form.value).forEach(([k, v]) => {
      if (v !== null && v !== undefined && v !== '') {
        if (typeof v === 'string') {
          fd.append(k, v);
        } else if (typeof v === 'number' || typeof v === 'boolean') {
          fd.append(k, v.toString());
        }
      }
    });
    if (this.selectedFile) fd.append('imagen', this.selectedFile);
    this.svc.createEvento(fd).subscribe({
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
      message: '¿Eliminar este evento?',
      accept: () => this.svc.deleteEvento(uuid).subscribe(() => this.load()),
    });
  }
}

function pubStyles(): string {
  return `
    .pub-container { display: flex; flex-direction: column; gap: 20px; }
    .page-header { padding: 20px 24px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .page-header h1 { margin: 0 0 4px 0; font-size: 22px; font-weight: 700; }
    .page-header p { margin: 0; opacity: 0.9; font-size: 13px; }
    .teal { background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%); }
    .table-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
    .table-caption { display: flex; justify-content: space-between; align-items: center; }
    .table-title { font-size: 16px; font-weight: 700; color: #1e293b; }
    .thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .empty-msg { text-align: center; padding: 20px; color: #64748b; }
    .estado-badge { font-size: 11px; padding: 3px 8px; border-radius: 9999px; background: #fef9c3; color: #854d0e; font-weight: 600; }
    .estado-badge.activo { background: #dcfce7; color: #166534; }
    .modal-form { display: flex; flex-direction: column; gap: 16px; margin-top: 12px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field.full { grid-column: 1 / -1; }
    .field label { font-size: 13px; font-weight: 600; color: #475569; }
    .file-input { border: 1px dashed #cbd5e1; padding: 10px; border-radius: 6px; cursor: pointer; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
  `;
}
