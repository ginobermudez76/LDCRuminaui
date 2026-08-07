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
import { PublicistaService, Curso, Deporte } from '../../../core/services/publicista.service';

@Component({
  selector: 'app-cursos',
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
  templateUrl: './cursos.component.html',
  styleUrl: './cursos.component.css',
})
export class CursosComponent implements OnInit {
  private svc = inject(PublicistaService);
  private fb = inject(FormBuilder);
  private confirm = inject(ConfirmationService);
  items = signal<Curso[]>([]);
  deportes = signal<Deporte[]>([]);
  showDialog = false;
  saving = signal(false);
  error = signal<string | undefined>(undefined);
  form!: FormGroup;
  private selectedFile: File | null = null;

  ngOnInit() {
    this.form = this.fb.group({
      nombre: ['', Validators.required],
      descripcion: [''],
      fecha_inicio: [''],
      fecha_fin: [''],
      deporte_id: [null],
      estado: ['Activo'],
      inscripciones: ['Activo'],
    });
    this.load();
    this.svc.getDeportes().subscribe((d) => this.deportes.set(d));
  }

  load() {
    this.svc.getCursos().subscribe((d) => this.items.set(d));
  }
  onFileChange(e: any) {
    this.selectedFile = e.target.files[0] || null;
  }
  openDialog() {
    this.form.reset({ estado: 'Activo', inscripciones: 'Activo' });
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
        const val = typeof v === 'object' ? JSON.stringify(v) : String(v);
        fd.append(k, val);
      }
    });
    if (this.selectedFile) fd.append('imagen', this.selectedFile);
    this.svc.createCurso(fd).subscribe({
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
      message: '¿Eliminar este curso?',
      accept: () => this.svc.deleteCurso(uuid).subscribe(() => this.load()),
    });
  }
}
