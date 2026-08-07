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
import {
  PublicistaService,
  DeportistaDestacado,
  Deporte,
} from '../../../core/services/publicista.service';

@Component({
  selector: 'app-deportistas',
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
  templateUrl: './deportistas.component.html',
  styleUrl: './deportistas.component.css',
})
export class DeportistasComponent implements OnInit {
  private readonly svc = inject(PublicistaService);
  private readonly fb = inject(FormBuilder);
  private readonly confirm = inject(ConfirmationService);
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
    this.svc.getDeportes().subscribe((d) => this.deportes.set(d));
  }

  load() {
    this.svc.getDeportistas().subscribe((d) => this.items.set(d));
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
    fd.append('nombre_deportista', this.form.value.nombre_deportista);
    if (this.form.value.deporte_id) fd.append('deporte_id', String(this.form.value.deporte_id));
    if (this.selectedFile) fd.append('imagen', this.selectedFile);
    this.svc.createDeportista(fd).subscribe({
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
      message: '¿Eliminar este deportista?',
      accept: () => this.svc.deleteDeportista(uuid).subscribe(() => this.load()),
    });
  }
}
