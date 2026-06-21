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
  template: `
    <div class="form-container">
      <div class="back-link">
        <a routerLink="/dashboard/solicitudes" class="back-btn"><i class="pi pi-arrow-left"></i> Volver al listado</a>
      </div>
      
      <p-card header="Crear Nueva Solicitud" styleClass="p-card-shadow">
        <form [formGroup]="solicitudForm" (ngSubmit)="onSubmit()" class="solicitud-form">
          <div class="form-grid">
            <!-- Tipo de Solicitud -->
            <div class="form-field full-width">
              <label for="tipo">Tipo de Solicitud *</label>
              <p-select 
                id="tipo" 
                [options]="tipos()" 
                optionLabel="name_tipo"
                optionValue="id_tipo"
                formControlName="tipo" 
                placeholder="Seleccione el tipo"
                [style]="{width: '100%'}"
              ></p-select>
              <small class="error-text" *ngIf="solicitudForm.get('tipo')?.invalid && solicitudForm.get('tipo')?.touched">
                El tipo de solicitud es requerido.
              </small>
            </div>

            <!-- Vista previa del flujo -->
            <div class="form-field full-width workflow-preview-card" *ngIf="selectedWorkflowSteps().length > 0">
              <span class="preview-title"><i class="pi pi-directions"></i> Ruta de evaluación obligatoria:</span>
              <div class="steps-flow">
                <div *ngFor="let s of selectedWorkflowSteps(); let last = last" class="flow-step">
                  <span class="step-lbl">{{ s.rol?.nombre_rol }}</span>
                  <i class="pi pi-chevron-right step-arrow" *ngIf="!last"></i>
                </div>
              </div>
            </div>

            <div class="form-field full-width workflow-preview-card no-steps" *ngIf="hasSelectedType() && selectedWorkflowSteps().length === 0">
              <span class="preview-title"><i class="pi pi-check-circle"></i> Aprobación Directa:</span>
              <p class="preview-desc">Esta solicitud no requiere pasos intermedios y se enviará directamente para aprobación final.</p>
            </div>

            <!-- Valor / Costo (Condicional) -->
            <div class="form-field" *ngIf="requiereValor()">
              <label for="valor">Valor Solicitado ($) *</label>
              <p-inputNumber 
                id="valor" 
                formControlName="s_valor" 
                mode="currency" 
                currency="USD" 
                locale="en-US"
                [style]="{width: '100%'}"
                [min]="0"
              ></p-inputNumber>
              <small class="error-text" *ngIf="solicitudForm.get('s_valor')?.invalid && solicitudForm.get('s_valor')?.touched">
                El valor es requerido y debe ser mayor o igual a 0.
              </small>
            </div>

            <!-- Documento Adjunto (Ruta o Nombre) (Condicional) -->
            <div class="form-field full-width" *ngIf="requiereDocumento()">
              <label for="doc">Ruta/Enlace del Documento Soporte *</label>
              <input 
                id="doc" 
                type="text" 
                pInputText 
                formControlName="s_doc" 
                placeholder="Ej. https://drive.google.com/..."
                class="w-full"
              />
              <small class="error-text" *ngIf="solicitudForm.get('s_doc')?.invalid && solicitudForm.get('s_doc')?.touched">
                El documento soporte es requerido.
              </small>
            </div>

            <!-- Descripción (Condicional) -->
            <div class="form-field full-width" *ngIf="requiereDescripcion()">
              <label for="descripcion">Detalle de la Solicitud *</label>
              <textarea 
                id="descripcion" 
                rows="4" 
                pInputText 
                formControlName="descripcion" 
                placeholder="Describa el motivo de su solicitud..."
                class="w-full text-area"
              ></textarea>
              <small class="error-text" *ngIf="solicitudForm.get('descripcion')?.invalid && solicitudForm.get('descripcion')?.touched">
                La descripción es requerida (máximo 255 caracteres).
              </small>
            </div>
          </div>

          <div class="error-wrapper" *ngIf="errorMessage()">
            <p-message severity="error" [text]="errorMessage()"></p-message>
          </div>

          <div class="form-actions">
            <button 
              pButton 
              type="button" 
              label="Cancelar" 
              icon="pi pi-times" 
              class="p-button-outlined p-button-secondary"
              routerLink="/dashboard/solicitudes"
            ></button>
            <button 
              pButton 
              type="submit" 
              label="Enviar Solicitud" 
              icon="pi pi-send" 
              [loading]="isLoading()"
              [disabled]="solicitudForm.invalid"
              class="p-button-success"
            ></button>
          </div>
        </form>
      </p-card>
    </div>
  `,
  styles: [`
    .form-container {
      max-width: 800px;
      margin: 0 auto;
    }
    .back-link {
      margin-bottom: 15px;
    }
    .back-btn {
      color: #1e3c72;
      text-decoration: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .back-btn:hover {
      text-decoration: underline;
    }
    .solicitud-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .form-grid {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .form-field {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .form-field label {
      font-weight: 600;
      color: #333;
      font-size: 14px;
    }
    .form-field.full-width {
      width: 100%;
    }
    .w-full {
      width: 100%;
    }
    .text-area {
      resize: vertical;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .error-text {
      color: #d32f2f;
      font-size: 12px;
    }
    .error-wrapper {
      margin-top: 10px;
    }
    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 20px;
      border-top: 1px solid #eee;
      padding-top: 20px;
    }
    .workflow-preview-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      padding: 14px;
      border-radius: 8px;
    }
    .workflow-preview-card.no-steps {
      border-left: 4px solid #16a34a;
      background: #f0fdf4;
    }
    .preview-title {
      font-weight: 700;
      font-size: 13px;
      color: #1e3c72;
      display: block;
      margin-bottom: 8px;
    }
    .preview-desc {
      margin: 0;
      font-size: 12px;
      color: #166534;
    }
    .steps-flow {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }
    .flow-step {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .step-lbl {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      color: #1e40af;
      padding: 4px 8px;
      font-size: 12px;
      font-weight: 600;
      border-radius: 4px;
    }
    .step-arrow {
      color: #64748b;
      font-size: 11px;
    }
  `]
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
    this.solicitudForm.get('tipo')?.valueChanges.subscribe(tipoId => {
      const selectedType = this.tipos().find(t => t.id_tipo === tipoId);
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
