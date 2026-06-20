import { Component, signal, inject } from '@angular/core';
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
            <div class="form-field">
              <label for="tipo">Tipo de Solicitud *</label>
              <p-select 
                id="tipo" 
                [options]="tipos" 
                formControlName="tipo" 
                placeholder="Seleccione el tipo"
                [style]="{width: '100%'}"
              ></p-select>
              <small class="error-text" *ngIf="solicitudForm.get('tipo')?.invalid && solicitudForm.get('tipo')?.touched">
                El tipo de solicitud es requerido.
              </small>
            </div>

            <!-- Valor / Costo -->
            <div class="form-field">
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

            <!-- Documento Adjunto (Ruta o Nombre) -->
            <div class="form-field full-width">
              <label for="doc">Ruta/Enlace del Documento Soporte (Opcional)</label>
              <input 
                id="doc" 
                type="text" 
                pInputText 
                formControlName="s_doc" 
                placeholder="Ej. https://drive.google.com/..."
                class="w-full"
              />
            </div>

            <!-- Descripción -->
            <div class="form-field full-width">
              <label for="descripcion">Detalle de la Solicitud</label>
              <textarea 
                id="descripcion" 
                rows="4" 
                pInputText 
                formControlName="descripcion" 
                placeholder="Describa el motivo de su solicitud..."
                class="w-full text-area"
              ></textarea>
              <small class="error-text" *ngIf="solicitudForm.get('descripcion')?.invalid && solicitudForm.get('descripcion')?.touched">
                La descripción no puede exceder los 255 caracteres.
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
      display: grid;
      grid-template-columns: 1fr 1fr;
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
      grid-column: span 2;
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
    @media (max-width: 600px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      .form-field.full-width {
        grid-column: span 1;
      }
    }
  `]
})
export class SolicitudFormComponent {
  private fb = inject(FormBuilder);
  private solicitudService = inject(SolicitudService);
  private router = inject(Router);

  solicitudForm: FormGroup;
  isLoading = signal(false);
  errorMessage = signal<string | undefined>(undefined);

  tipos = [
    { label: 'Ayudantía Económica', value: 1 },
    { label: 'Inscripción a Evento', value: 2 },
    { label: 'Permiso de Deporte', value: 3 },
    { label: 'Otros', value: 4 }
  ];

  constructor() {
    this.solicitudForm = this.fb.group({
      tipo: [null, Validators.required],
      s_valor: [0, [Validators.required, Validators.min(0)]],
      s_doc: [''],
      descripcion: ['', [Validators.maxLength(255)]]
    });
  }

  onSubmit() {
    if (this.solicitudForm.invalid) return;

    this.isLoading.set(true);
    this.errorMessage.set(undefined);

    this.solicitudService.createSolicitud(this.solicitudForm.value).subscribe({
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
