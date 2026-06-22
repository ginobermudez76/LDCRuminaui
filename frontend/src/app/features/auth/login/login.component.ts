import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { MessageModule } from 'primeng/message';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    ReactiveFormsModule,
    InputTextModule,
    ButtonModule,
    MessageModule
  ],
  template: `
    <div class="login-page">
      <a [routerLink]="['/']" class="back-btn">
        <span class="material-symbols-outlined">arrow_back</span>
        Volver al inicio
      </a>

      <div class="login-container">
        <div class="login-logo-wrap">
          <img src="http://localhost:8080/img/logoX_LDCR.png" alt="Logo LDCR"
               class="login-logo" onerror="this.style.display='none'" />
          <h2 class="login-brand">Liga Cantonal Rumiñahui</h2>
          <p class="login-sub">Panel de Gestión Deportiva</p>
        </div>

        <div class="login-card">
          <h3 class="card-title">
            <span class="material-symbols-outlined">lock</span>
            Iniciar Sesión
          </h3>
          <form [formGroup]="loginForm" (ngSubmit)="onSubmit()" class="login-form">
            <div class="p-field">
              <label for="username">
                <span class="material-symbols-outlined">person</span>
                Nombre de Usuario
              </label>
              <input
                id="username"
                type="text"
                pInputText
                formControlName="nombre_usuario"
                placeholder="Ingrese su usuario"
                class="ldcr-input"
              />
              <small class="error-text" *ngIf="loginForm.get('nombre_usuario')?.invalid && loginForm.get('nombre_usuario')?.touched">
                El usuario es requerido.
              </small>
            </div>

            <div class="p-field">
              <label for="password">
                <span class="material-symbols-outlined">key</span>
                Contraseña
              </label>
              <input
                id="password"
                type="password"
                pInputText
                formControlName="contrasena"
                placeholder="Ingrese su contraseña"
                class="ldcr-input"
              />
              <small class="error-text" *ngIf="loginForm.get('contrasena')?.invalid && loginForm.get('contrasena')?.touched">
                La contraseña es requerida (mínimo 6 caracteres).
              </small>
            </div>

            <div class="error-container" *ngIf="errorMessage()">
              <p-message severity="error" [text]="errorMessage()!"></p-message>
            </div>

            <button
              pButton
              type="submit"
              [loading]="isLoading()"
              [disabled]="loginForm.invalid"
              class="ldcr-btn w-full"
            >
              <span class="material-symbols-outlined" *ngIf="!isLoading()">login</span>
              Iniciar Sesión
            </button>
          </form>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .login-page {
      min-height: 100vh;
      background: linear-gradient(to bottom, #030022, #11637c);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      position: relative;
    }
    .back-btn {
      position: fixed;
      top: 20px; left: 24px;
      display: flex; align-items: center; gap: 6px;
      color: rgba(255,255,255,0.8);
      font-size: 14px; font-weight: 600;
      text-decoration: none;
      padding: 8px 16px; border-radius: 8px;
      border: 1px solid rgba(15,195,198,0.3);
      background: rgba(15,195,198,0.08);
      transition: all 0.2s; z-index: 10;
    }
    .back-btn .material-symbols-outlined { font-size: 18px; color: #0fc3c6; }
    .back-btn:hover { background: rgba(15,195,198,0.18); border-color: #0fc3c6; color: #fff; }
    .login-container {
      width: 100%; max-width: 420px;
      display: flex; flex-direction: column; align-items: center; gap: 28px;
    }
    .login-logo-wrap { text-align: center; }
    .login-logo { width: 90px; height: 90px; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4)); margin-bottom: 12px; }
    .login-brand { font-family: 'Pattaya', 'Lobster', cursive; font-size: 20px; color: #fff; margin: 0 0 4px; }
    .login-sub { font-size: 13px; color: rgba(255,255,255,0.6); margin: 0; }
    .login-card {
      width: 100%;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(15,195,198,0.3);
      border-top: 3px solid #0fc3c6;
      border-radius: 12px;
      padding: 32px 36px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }
    .card-title {
      display: flex; align-items: center; gap: 10px;
      font-size: 20px; font-weight: 700; color: #0fc3c6;
      margin: 0 0 28px; font-family: 'Bebas Neue', cursive; letter-spacing: 1px;
    }
    .card-title .material-symbols-outlined { font-size: 22px; }
    .login-form { display: flex; flex-direction: column; gap: 20px; }
    .p-field { display: flex; flex-direction: column; gap: 8px; }
    .p-field label {
      display: flex; align-items: center; gap: 6px;
      font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.85);
    }
    .p-field label .material-symbols-outlined { font-size: 16px; color: #0fc3c6; }
    .ldcr-input {
      width: 100%; height: 46px; padding: 0 14px;
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(15,195,198,0.3);
      border-radius: 8px; color: #fff; font-size: 14px;
      transition: border-color 0.2s, background 0.2s;
    }
    .ldcr-input::placeholder { color: rgba(255,255,255,0.35); }
    .ldcr-input:focus { border-color: #0fc3c6; background: rgba(15,195,198,0.08); outline: none; box-shadow: 0 0 0 3px rgba(15,195,198,0.15); }
    .error-text { color: #ff6b6b; font-size: 12px; }
    .error-container { margin-top: 4px; }
    .ldcr-btn {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      background: #0fc3c6 !important; border: none !important; border-radius: 8px !important;
      color: #030022 !important; font-weight: 700 !important; font-size: 15px !important;
      height: 48px; cursor: pointer; transition: all 0.2s; margin-top: 4px;
    }
    .ldcr-btn .material-symbols-outlined { font-size: 18px; }
    .ldcr-btn:hover:not(:disabled) { background: #0aa8ab !important; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(15,195,198,0.4) !important; }
    .ldcr-btn:disabled { opacity: 0.6; }
    .w-full { width: 100%; }
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
      next: () => {
        this.isLoading.set(false);
        this.router.navigate(['/dashboard']);
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set(err.error?.error || 'Usuario o contraseña incorrectos.');
      }
    });
  }
}
