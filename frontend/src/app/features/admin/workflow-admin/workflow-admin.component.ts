import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormArray, ReactiveFormsModule, Validators } from '@angular/forms';
import { CardModule } from 'primeng/card';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { SelectModule } from 'primeng/select';
import { MessageModule } from 'primeng/message';
import { CheckboxModule } from 'primeng/checkbox';
import { SolicitudTipoService, SolicitudTipo, Rol, WorkflowStep } from '../../../core/services/solicitud-tipo.service';

@Component({
  selector: 'app-workflow-admin',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    CardModule,
    InputTextModule,
    ButtonModule,
    SelectModule,
    MessageModule,
    CheckboxModule
  ],
  templateUrl: './workflow-admin.component.html',
  styleUrl: './workflow-admin.component.css'
})
export class WorkflowAdminComponent implements OnInit {
  private fb = inject(FormBuilder);
  private service = inject(SolicitudTipoService);

  tipos = signal<SolicitudTipo[]>([]);
  roles = signal<Rol[]>([]);
  selectedTipoId = signal<number | null>(null);
  isLoading = signal(false);
  errorMessage = signal<string | undefined>(undefined);

  tipoForm!: FormGroup;

  ngOnInit() {
    this.initForm();
    this.loadTipos();
    this.loadRoles();
  }

  private initForm() {
    this.tipoForm = this.fb.group({
      name_tipo: ['', Validators.required],
      requiere_documento: [false],
      requiere_valor: [false],
      requiere_descripcion: [true],
      activo: [true],
      steps: this.fb.array([])
    });
  }

  get stepsArray(): FormArray {
    return this.tipoForm.get('steps') as FormArray;
  }

  addStep(step?: WorkflowStep) {
    const stepGroup = this.fb.group({
      orden: [step ? step.orden : this.stepsArray.length + 1, Validators.required],
      rol_id: [step ? step.rol_id : null, Validators.required],
      nombre_paso: [step ? step.nombre_paso : '']
    });
    this.stepsArray.push(stepGroup);
  }

  removeStep(index: number) {
    this.stepsArray.removeAt(index);
    // Recalculate ordering
    this.stepsArray.controls.forEach((group, idx) => {
      group.get('orden')?.setValue(idx + 1);
    });
  }

  loadTipos() {
    this.service.getSolicitudTipos().subscribe({
      next: (data) => this.tipos.set(data),
      error: () => this.errorMessage.set('Error al cargar tipos de solicitud')
    });
  }

  loadRoles() {
    this.service.getRoles().subscribe({
      next: (data) => this.roles.set(data),
      error: () => this.errorMessage.set('Error al cargar roles')
    });
  }

  onSelectTipo(tipo: SolicitudTipo) {
    this.selectedTipoId.set(tipo.id_tipo);
    this.errorMessage.set(undefined);

    // Patch basic info
    this.tipoForm.patchValue({
      name_tipo: tipo.name_tipo,
      requiere_documento: tipo.requiere_documento,
      requiere_valor: tipo.requiere_valor,
      requiere_descripcion: tipo.requiere_descripcion,
      activo: tipo.activo
    });

    // Clear and reload steps array
    this.stepsArray.clear();
    if (tipo.steps && tipo.steps.length > 0) {
      tipo.steps.forEach(step => this.addStep(step));
    }
  }

  onNewTipo() {
    this.selectedTipoId.set(null);
    this.errorMessage.set(undefined);
    this.tipoForm.reset({
      name_tipo: '',
      requiere_documento: false,
      requiere_valor: false,
      requiere_descripcion: true,
      activo: true
    });
    this.stepsArray.clear();
  }

  onSubmit() {
    if (this.tipoForm.invalid) return;

    this.isLoading.set(true);
    this.errorMessage.set(undefined);

    const formData = this.tipoForm.value;

    if (this.selectedTipoId()) {
      // Update existing
      this.service.updateSolicitudTipo(this.selectedTipoId()!, formData).subscribe({
        next: () => {
          this.isLoading.set(false);
          this.loadTipos();
          this.onNewTipo(); // reset form
        },
        error: (err: any) => {
          this.isLoading.set(false);
          this.errorMessage.set(err.error?.error || 'Error al actualizar');
        }
      });
    } else {
      // Create new
      this.service.createSolicitudTipo(formData).subscribe({
        next: () => {
          this.isLoading.set(false);
          this.loadTipos();
          this.onNewTipo(); // reset form
        },
        error: (err: any) => {
          this.isLoading.set(false);
          this.errorMessage.set(err.error?.error || 'Error al crear');
        }
      });
    }
  }
}
