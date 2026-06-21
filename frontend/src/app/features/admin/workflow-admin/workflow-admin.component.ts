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
  template: `
    <div class="workflow-container">
      <header class="page-header">
        <h1 class="title">Configuración de Trámites y Flujos</h1>
        <p class="subtitle">Administra los tipos de solicitudes, requisitos de campos y el orden del flujo de aprobación de roles.</p>
      </header>

      <div class="content-grid">
        <!-- Listado de tipos de solicitud (Izquierda) -->
        <div class="panel-left">
          <p-card header="Tipos de Solicitudes" styleClass="glass-card">
            <div class="action-bar">
              <button 
                pButton 
                type="button" 
                label="Nuevo Tipo" 
                icon="pi pi-plus" 
                class="p-button-primary p-button-sm w-full"
                (click)="onNewTipo()"
              ></button>
            </div>

            <div class="tipo-list">
              <div 
                *ngFor="let t of tipos()" 
                class="tipo-item" 
                [class.active]="selectedTipoId() === t.id_tipo"
                (click)="onSelectTipo(t)"
              >
                <div class="tipo-info">
                  <span class="tipo-name">{{ t.name_tipo }}</span>
                  <span class="status-badge" [class.inactive]="!t.activo">
                    {{ t.activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
                <div class="tipo-meta">
                  <span *ngIf="t.requiere_documento" class="meta-tag"><i class="pi pi-file"></i> Doc</span>
                  <span *ngIf="t.requiere_valor" class="meta-tag"><i class="pi pi-dollar"></i> Valor</span>
                  <span *ngIf="t.requiere_descripcion" class="meta-tag"><i class="pi pi-align-left"></i> Desc</span>
                </div>
                <div class="steps-preview" *ngIf="t.steps && t.steps.length > 0">
                  <span class="preview-title">Flujo:</span>
                  <span class="preview-path">
                    <ng-container *ngFor="let s of t.steps; let last = last">
                      {{ s.rol?.nombre_rol }}{{ !last ? ' ➔ ' : '' }}
                    </ng-container>
                  </span>
                </div>
                <div class="steps-preview no-steps" *ngIf="!t.steps || t.steps.length === 0">
                  <span class="preview-title">Sin flujo asignado (Aprobación directa)</span>
                </div>
              </div>
            </div>
          </p-card>
        </div>

        <!-- Formulario de Configuración (Derecha) -->
        <div class="panel-right">
          <p-card [header]="selectedTipoId() ? 'Editar Tipo de Solicitud' : 'Nuevo Tipo de Solicitud'" styleClass="glass-card">
            <form [formGroup]="tipoForm" (ngSubmit)="onSubmit()" class="workflow-form">
              
              <!-- Campos Básicos -->
              <div class="form-section">
                <h3 class="section-title">Datos Básicos</h3>
                <div class="form-field">
                  <label for="name_tipo">Nombre del Tipo de Solicitud *</label>
                  <input 
                    id="name_tipo" 
                    type="text" 
                    pInputText 
                    formControlName="name_tipo" 
                    placeholder="Ej. Ayudantía Económica Especial"
                    class="w-full"
                  />
                  <small class="error-text" *ngIf="tipoForm.get('name_tipo')?.invalid && tipoForm.get('name_tipo')?.touched">
                    El nombre es requerido.
                  </small>
                </div>

                <!-- Flags de Requisitos -->
                <div class="checkbox-group">
                  <div class="check-item">
                    <p-checkbox formControlName="requiere_documento" [binary]="true" inputId="req_doc"></p-checkbox>
                    <label for="req_doc">Requiere subir documento soporte</label>
                  </div>
                  <div class="check-item">
                    <p-checkbox formControlName="requiere_valor" [binary]="true" inputId="req_val"></p-checkbox>
                    <label for="req_val">Requiere especificar valor monetario</label>
                  </div>
                  <div class="check-item">
                    <p-checkbox formControlName="requiere_descripcion" [binary]="true" inputId="req_desc"></p-checkbox>
                    <label for="req_desc">Requiere descripción o motivo</label>
                  </div>
                  <div class="check-item" *ngIf="selectedTipoId()">
                    <p-checkbox formControlName="activo" [binary]="true" inputId="is_active"></p-checkbox>
                    <label for="is_active">Activo</label>
                  </div>
                </div>
              </div>

              <!-- Configuración de Pasos de Flujo -->
              <div class="form-section steps-section">
                <div class="section-header">
                  <h3 class="section-title">Flujo de Aprobación</h3>
                  <button 
                    pButton 
                    type="button" 
                    label="Agregar Paso" 
                    icon="pi pi-plus" 
                    class="p-button-outlined p-button-sm p-button-secondary"
                    (click)="addStep()"
                  ></button>
                </div>

                <div formArrayName="steps" class="steps-list-builder">
                  <div *ngIf="stepsArray.length === 0" class="no-steps-placeholder">
                    <i class="pi pi-info-circle"></i> No hay pasos definidos. Esta solicitud se aprobará automáticamente.
                  </div>

                  <div 
                    *ngFor="let s of stepsArray.controls; let i = index" 
                    [formGroupName]="i" 
                    class="step-builder-item"
                  >
                    <span class="step-badge">{{ i + 1 }}</span>
                    
                    <!-- Nombre del Paso -->
                    <div class="step-field flex-grow">
                      <input 
                        type="text" 
                        pInputText 
                        formControlName="nombre_paso" 
                        placeholder="Nombre del paso (opcional)" 
                        class="w-full input-sm"
                      />
                    </div>

                    <!-- Selección de Rol -->
                    <div class="step-field">
                      <p-select 
                        [options]="roles()" 
                        optionLabel="nombre_rol"
                        optionValue="id"
                        formControlName="rol_id" 
                        placeholder="Rol Evaluador"
                        [style]="{width: '100%'}"
                      ></p-select>
                    </div>

                    <!-- Acciones -->
                    <button 
                      pButton 
                      type="button" 
                      icon="pi pi-trash" 
                      class="p-button-text p-button-danger p-button-sm"
                      (click)="removeStep(i)"
                    ></button>
                  </div>
                </div>
              </div>

              <div class="error-wrapper" *ngIf="errorMessage()">
                <p-message severity="error" [text]="errorMessage() || ''"></p-message>
              </div>

              <div class="form-actions">
                <button 
                  pButton 
                  type="submit" 
                  [label]="selectedTipoId() ? 'Guardar Cambios' : 'Crear Tipo'" 
                  icon="pi pi-save" 
                  [loading]="isLoading()"
                  [disabled]="tipoForm.invalid"
                  class="p-button-success"
                ></button>
              </div>

            </form>
          </p-card>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .workflow-container {
      display: flex;
      flex-direction: column;
      gap: 24px;
      padding: 10px;
    }
    .page-header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      padding: 24px;
      border-radius: 12px;
      color: white;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .title {
      margin: 0 0 8px 0;
      font-size: 24px;
      font-weight: 700;
    }
    .subtitle {
      margin: 0;
      opacity: 0.9;
      font-size: 14px;
    }
    .content-grid {
      display: grid;
      grid-template-columns: 350px 1fr;
      gap: 24px;
      align-items: start;
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .action-bar {
      margin-bottom: 16px;
    }
    .tipo-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-height: 550px;
      overflow-y: auto;
    }
    .tipo-item {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 14px;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .tipo-item:hover {
      border-color: #1e3c72;
      background: #f1f5f9;
    }
    .tipo-item.active {
      border-color: #1e3c72;
      background: #eff6ff;
      box-shadow: 0 0 0 1px #1e3c72;
    }
    .tipo-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 6px;
    }
    .tipo-name {
      font-weight: 700;
      color: #1e293b;
      font-size: 15px;
    }
    .status-badge {
      font-size: 11px;
      padding: 2px 6px;
      border-radius: 9999px;
      background: #dcfce7;
      color: #166534;
      font-weight: 600;
    }
    .status-badge.inactive {
      background: #fee2e2;
      color: #991b1b;
    }
    .tipo-meta {
      display: flex;
      gap: 8px;
      margin-bottom: 8px;
    }
    .meta-tag {
      font-size: 11px;
      background: #e2e8f0;
      color: #475569;
      padding: 2px 6px;
      border-radius: 4px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .steps-preview {
      border-top: 1px dashed #e2e8f0;
      padding-top: 8px;
      font-size: 12px;
      color: #64748b;
    }
    .preview-title {
      font-weight: 700;
      margin-right: 4px;
      color: #475569;
    }
    .preview-path {
      font-style: italic;
    }
    .no-steps {
      color: #b91c1c;
      font-style: italic;
    }
    .workflow-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .form-section {
      border-bottom: 1px solid #f1f5f9;
      padding-bottom: 20px;
    }
    .section-title {
      font-size: 16px;
      font-weight: 700;
      color: #1e3c72;
      margin: 0 0 16px 0;
    }
    .form-field {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .form-field label {
      font-weight: 600;
      font-size: 13px;
      color: #475569;
    }
    .checkbox-group {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-top: 16px;
    }
    .check-item {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .check-item label {
      font-size: 14px;
      color: #334155;
      cursor: pointer;
    }
    .steps-section {
      border-bottom: none;
    }
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }
    .steps-list-builder {
      display: flex;
      flex-direction: column;
      gap: 12px;
      background: #f8fafc;
      padding: 16px;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }
    .no-steps-placeholder {
      text-align: center;
      padding: 20px;
      color: #94a3b8;
      font-size: 13px;
      font-style: italic;
    }
    .step-builder-item {
      display: flex;
      align-items: center;
      gap: 12px;
      background: white;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .step-badge {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: #1e3c72;
      color: white;
      font-weight: 700;
      font-size: 12px;
    }
    .flex-grow {
      flex-grow: 1;
    }
    .input-sm {
      padding: 6px 10px;
      font-size: 13px;
    }
    .error-text {
      color: #dc2626;
      font-size: 11px;
    }
    .form-actions {
      display: flex;
      justify-content: flex-end;
      margin-top: 10px;
    }
    .w-full {
      width: 100%;
    }
    @media (max-width: 900px) {
      .content-grid {
        grid-template-columns: 1fr;
      }
    }
  `]
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
