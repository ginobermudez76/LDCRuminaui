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
import { PublicistaService, CartaCondolencia } from '../../../core/services/publicista.service';

@Component({
  selector: 'app-cartas-condolencia',
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
  templateUrl: './cartas-condolencia.component.html',
  styleUrl: './cartas-condolencia.component.css',
})
export class CartasCondolenciaComponent implements OnInit {
  private readonly svc = inject(PublicistaService);
  private readonly fb = inject(FormBuilder);
  private readonly confirm = inject(ConfirmationService);
  items = signal<CartaCondolencia[]>([]);
  showDialog = false;
  saving = signal(false);
  error = signal<string | undefined>(undefined);
  form!: FormGroup;
  private selectedFile: File | null = null;

  ngOnInit() {
    this.form = this.fb.group({
      mensaje: ['', Validators.required],
      fecha_eliminar: ['', Validators.required],
    });
    this.load();
  }

  load() {
    this.svc.getCartas().subscribe((d) => this.items.set(d));
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
    fd.append('mensaje', this.form.value.mensaje);
    fd.append('fecha_eliminar', this.form.value.fecha_eliminar);
    if (this.selectedFile) fd.append('imagen', this.selectedFile);
    this.svc.createCarta(fd).subscribe({
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
      message: '¿Eliminar esta carta?',
      accept: () => this.svc.deleteCarta(uuid).subscribe(() => this.load()),
    });
  }
}
