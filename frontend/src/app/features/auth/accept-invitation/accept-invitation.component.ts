import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { MessageModule } from 'primeng/message';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { CardModule } from 'primeng/card';
import { UsuarioService } from '../../../core/services/usuario.service';

@Component({
  selector: 'app-accept-invitation',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterLink,
    MessageModule,
    InputTextModule,
    ButtonModule,
    CardModule
  ],
  templateUrl: './accept-invitation.component.html',
  styleUrl: './accept-invitation.component.css'
})
export class AcceptInvitationComponent implements OnInit {
  private fb = inject(FormBuilder);
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private usuarioService = inject(UsuarioService);

  form!: FormGroup;
  token = '';
  loading = signal(false);
  success = signal(false);
  error = signal<string | null>(null);

  ngOnInit() {
    this.token = this.route.snapshot.queryParams['token'] || '';
    if (!this.token) {
      this.error.set('Token de invitación inválido o no suministrado.');
    }

    this.form = this.fb.group({
      contrasena: ['', [Validators.required, Validators.minLength(6)]],
      confirmar_contrasena: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  private passwordMatchValidator(g: FormGroup) {
    return g.get('contrasena')?.value === g.get('confirmar_contrasena')?.value
      ? null : { mismatch: true };
  }

  onSubmit() {
    if (this.form.invalid || !this.token) return;

    this.loading.set(true);
    this.error.set(null);

    const contrasena = this.form.get('contrasena')?.value;

    this.usuarioService.acceptInvitation(this.token, contrasena).subscribe({
      next: (res) => {
        this.loading.set(false);
        this.success.set(true);
      },
      error: (err) => {
        this.loading.set(false);
        const errMsg = err.error?.error || 'No se pudo procesar la invitación. El enlace puede haber expirado.';
        this.error.set(errMsg);
      }
    });
  }
}
