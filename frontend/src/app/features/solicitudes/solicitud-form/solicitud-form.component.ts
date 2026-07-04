import { Component, signal, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CardModule } from 'primeng/card';
import { InputTextModule } from 'primeng/inputtext';
import { InputNumberModule } from 'primeng/inputnumber';
import { SelectModule } from 'primeng/select';
import { ButtonModule } from 'primeng/button';
import { MessageModule } from 'primeng/message';
import { SolicitudService } from '../../../core/services/solicitud.service';
import { SolicitudTipoService, SolicitudTipo, WorkflowStep } from '../../../core/services/solicitud-tipo.service';

@Component({
  selector: 'app-solicitud-form',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    ReactiveFormsModule,
    CardModule,
    InputTextModule,
    InputNumberModule,
    SelectModule,
    ButtonModule,
    MessageModule
  ],
  templateUrl: './solicitud-form.component.html',
  styleUrl: './solicitud-form.component.css'
})
export class SolicitudFormComponent implements OnInit {
  private fb = inject(FormBuilder);
  private solicitudService = inject(SolicitudService);
  private tipoService = inject(SolicitudTipoService);
  private router = inject(Router);

  solicitudForm!: FormGroup;
  isLoading = signal(false);
  errorMessage = signal<string | undefined>(undefined);

  tipos = signal<SolicitudTipo[]>([]);
  selectedWorkflowSteps = signal<WorkflowStep[]>([]);
  hasSelectedType = signal(false);

  // Field display signals
  requiereValor = signal(false);
  requiereDocumento = signal(false);
  requiereDescripcion = signal(true);

  ngOnInit() {
    this.initForm();
    this.loadTipos();
    
    // Subscribe to type changes
    this.solicitudForm.get('tipo')?.valueChanges.subscribe(tipoUuid => {
      const selectedType = this.tipos().find(t => t.uuid === tipoUuid);
      this.hasSelectedType.set(!!selectedType);
      this.updateValidators(selectedType);
    });
  }

  private initForm() {
    this.solicitudForm = this.fb.group({
      tipo: [null, Validators.required],
      s_valor: [0],
      s_doc: [''],
      descripcion: ['']
    });
  }

  loadTipos() {
    this.tipoService.getSolicitudTipos().subscribe({
      next: (data) => {
        // Only show active types to the users creating requests
        this.tipos.set(data.filter(t => t.activo));
      },
      error: () => this.errorMessage.set('Error al cargar los tipos de solicitudes.')
    });
  }

  updateValidators(type: SolicitudTipo | undefined) {
    if (!type) {
      this.requiereValor.set(false);
      this.requiereDocumento.set(false);
      this.requiereDescripcion.set(true);
      this.selectedWorkflowSteps.set([]);
      return;
    }

    const valorControl = this.solicitudForm.get('s_valor');
    const docControl = this.solicitudForm.get('s_doc');
    const descControl = this.solicitudForm.get('descripcion');

    // Reset controls values first
    valorControl?.setValue(0);
    docControl?.setValue('');
    descControl?.setValue('');

    // Valor validation
    if (type.requiere_valor) {
      valorControl?.setValidators([Validators.required, Validators.min(0)]);
    } else {
      valorControl?.clearValidators();
    }
    valorControl?.updateValueAndValidity();

    // Document validation
    if (type.requiere_documento) {
      docControl?.setValidators([Validators.required]);
    } else {
      docControl?.clearValidators();
    }
    docControl?.updateValueAndValidity();

    // Description validation
    if (type.requiere_descripcion) {
      descControl?.setValidators([Validators.required, Validators.maxLength(255)]);
    } else {
      descControl?.clearValidators();
    }
    descControl?.updateValueAndValidity();

    // Set UI indicators
    this.requiereValor.set(type.requiere_valor);
    this.requiereDocumento.set(type.requiere_documento);
    this.requiereDescripcion.set(type.requiere_descripcion);
    this.selectedWorkflowSteps.set(type.steps || []);
  }

  onSubmit() {
    if (this.solicitudForm.invalid) return;

    this.isLoading.set(true);
    this.errorMessage.set(undefined);

    const payload = { ...this.solicitudForm.value };
    
    // Clean up payloads fields if they are not required
    if (!this.requiereValor()) delete payload.s_valor;
    if (!this.requiereDocumento()) delete payload.s_doc;
    if (!this.requiereDescripcion()) delete payload.descripcion;

    this.solicitudService.createSolicitud(payload).subscribe({
      next: () => {
        this.isLoading.set(false);
        this.router.navigate(['/dashboard/solicitudes']);
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(
          err.error?.message || 'Error al procesar y auto-asignar la solicitud.'
        );
      }
    });
  }
}
