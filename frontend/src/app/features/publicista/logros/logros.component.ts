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
import { PublicistaService, Logro, Deporte } from '../../../core/services/publicista.service';

@Component({
  selector: 'app-logros',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, TableModule, DialogModule, ButtonModule, InputTextModule, SelectModule, MessageModule, ConfirmDialogModule],
  providers: [ConfirmationService],
  templateUrl: './logros.component.html',
  styleUrl: './logros.component.css'
})
export class LogrosComponent implements OnInit {
  private svc = inject(PublicistaService);
  private fb = inject(FormBuilder);
  private confirm = inject(ConfirmationService);
  items = signal<Logro[]>([]);
  deportes = signal<Deporte[]>([]);
  showDialog = false;
  saving = signal(false);
  error = signal<string | undefined>(undefined);
  form!: FormGroup;
  private selectedFile: File | null = null;

  ngOnInit() {
    this.form = this.fb.group({ titulo: ['', Validators.required], tipologro: [null], deporte_id: [null] });
    this.load();
    this.svc.getDeportes().subscribe(d => this.deportes.set(d));
  }

  load() { this.svc.getLogros().subscribe(d => this.items.set(d)); }
  onFileChange(e: any) { this.selectedFile = e.target.files[0] || null; }
  openDialog() { this.form.reset(); this.selectedFile = null; this.error.set(undefined); this.showDialog = true; }

  onSubmit() {
    if (this.form.invalid) return;
    this.saving.set(true);
    const fd = new FormData();
    fd.append('titulo', this.form.value.titulo);
    if (this.form.value.tipologro) fd.append('tipologro', this.form.value.tipologro);
    if (this.form.value.deporte_id) fd.append('deporte_id', String(this.form.value.deporte_id));
    if (this.selectedFile) fd.append('imagen', this.selectedFile);
    this.svc.createLogro(fd).subscribe({ next: () => { this.saving.set(false); this.showDialog = false; this.load(); }, error: (e) => { this.saving.set(false); this.error.set(e.error?.error || 'Error al guardar'); }});
  }

  confirmDelete(id: number) {
    this.confirm.confirm({ message: '¿Eliminar este logro?', accept: () => this.svc.deleteLogro(id).subscribe(() => this.load()) });
  }
}
