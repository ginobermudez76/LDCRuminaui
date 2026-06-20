import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { CardModule } from 'primeng/card';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { MessageModule } from 'primeng/message';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    CardModule,
    InputTextModule,
    ButtonModule,
    MessageModule
  ],
  template: `
    <div class="login-container">
      <div class="login-card-wrapper">
        <p-card header="Liga Cantonal Rumiñahui" subheader="Panel de Administración e Historial" styleClass="p-card-shadow custom-card">
          <form [formGroup]="loginForm" (ngSubmit)="onSubmit()" class="login-form">
            <div class="p-field">
              <label for="username">Nombre de Usuario</label>
              <div class="input-icon-wrapper">
                <i class="pi pi-user input-icon"></i>
                <input 
                  id="username" 
                  type="text" 
                  pInputText 
                  formControlName="nombre_usuario" 
                  placeholder="Ingrese su usuario"
                  class="w-full"
                />
              </div>
              <small class="error-text" *ngIf="loginForm.get('nombre_usuario')?.invalid && loginForm.get('nombre_usuario')?.touched">
                El usuario es requerido.
              </small>
            </div>

            <div class="p-field">
              <label for="password">Contraseña</label>
              <div class="input-icon-wrapper">
                <i class="pi pi-lock input-icon"></i>
                <input 
                  id="password" 
                  type="password" 
                  pInputText 
                  formControlName="contrasena" 
                  placeholder="Ingrese su contraseña"
                  class="w-full"
                />
              </div>
              <small class="error-text" *ngIf="loginForm.get('contrasena')?.invalid && loginForm.get('contrasena')?.touched">
                La contraseña es requerida (mínimo 6 caracteres).
              </small>
            </div>

            <div class="error-container" *ngIf="errorMessage()">
              <p-message severity="error" [text]="errorMessage()"></p-message>
            </div>

            <div class="button-wrapper">
              <button 
                pButton 
                type="submit" 
                label="Iniciar Sesión" 
                icon="pi pi-sign-in" 
                [loading]="isLoading()"
                [disabled]="loginForm.invalid"
                class="w-full p-button-primary"
              ></button>
            </div>
          </form>
        </p-card>
      </div>
    </div>
  `,
  styles: [`
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      padding: 20px;
    }
    .login-card-wrapper {
      width: 100%;
      max-width: 450px;
    }
    ::ng-deep .custom-card {
      background: rgba(255, 255, 255, 0.9) !important;
      backdrop-filter: blur(10px);
      border-radius: 12px !important;
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2) !important;
      border: 1px solid rgba(255, 255, 255, 0.18) !important;
    }
    ::ng-deep .custom-card .p-card-title {
      font-size: 24px;
      color: #1e3c72;
      text-align: center;
      font-weight: 700;
    }
    ::ng-deep .custom-card .p-card-subtitle {
      text-align: center;
      margin-bottom: 25px;
      color: #666;
    }
    .login-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .p-field {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .p-field label {
      font-weight: 600;
      color: #333;
      font-size: 14px;
    }
    .input-icon-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    .input-icon {
      position: absolute;
      left: 12px;
      color: #888;
    }
    .input-icon-wrapper input {
      padding-left: 36px;
      width: 100%;
      height: 45px;
      border-radius: 6px;
      border: 1px solid #ccc;
      transition: border-color 0.2s;
    }
    .input-icon-wrapper input:focus {
      border-color: #1e3c72;
      outline: none;
      box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
    }
    .error-text {
      color: #d32f2f;
      font-size: 12px;
    }
    .error-container {
      margin-top: 10px;
    }
    .button-wrapper {
      margin-top: 15px;
    }
    ::ng-deep .p-button {
      background: #1e3c72 !important;
      border-color: #1e3c72 !important;
      height: 45px;
      border-radius: 6px;
      font-weight: 600;
      transition: background 0.2s;
    }
    ::ng-deep .p-button:hover {
      background: #2a5298 !important;
      border-color: #2a5298 !important;
    }
    .w-full {
      width: 100%;
    }
  `]
})
export class LoginComponent {
  loginForm: FormGroup;
  isLoading = signal(false);
  errorMessage = signal<string | undefined>(undefined);

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      nombre_usuario: ['', Validators.required],
      contrasena: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onSubmit() {
    if (this.loginForm.invalid) return;

    this.isLoading.set(true);
    this.errorMessage.set(undefined);

    const { nombre_usuario, contrasena } = this.loginForm.value;

    this.authService.login(nombre_usuario, contrasena).subscribe({
      next: (response) => {
        this.isLoading.set(false);
        this.router.navigate(['/dashboard']);
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(
          err.error?.error || 'Usuario o contraseña incorrectos.'
        );
      }
    });
  }
}
