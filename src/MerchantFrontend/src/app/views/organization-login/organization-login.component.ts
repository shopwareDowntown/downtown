import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LoginService } from '../../core/services/login.service';
import { Router } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { ToastService } from '../../core/services/toast.service';

@Component({
  selector: 'portal-organization-login',
  templateUrl: './organization-login.component.html',
  styleUrls: ['./organization-login.component.scss']
})
export class OrganizationLoginComponent implements OnInit{
  loginForm: FormGroup;
  isLogging = false;
  loginFailed = false;

  private initialFormState: any;

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly loginService: LoginService,
    private readonly router: Router,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) {}

  ngOnInit(): void {
    this.loginForm = this.formBuilder.group({
      username: ['', Validators.required],
      password: ['', Validators.required]
    });
    this.initialFormState = this.loginForm.value;
  }

  enterLogin($event: KeyboardEvent) {
    if ($event.code === 'Enter') {
      this.doLogin();
    }
  }

  doLogin() {
    this.loginService.organizationLogin(
      this.loginForm.get('username').value,
      this.loginForm.get('password').value
    ).subscribe(() => {
      this.router.navigate(['/organization/home']);
      this.toastService.success(
        this.translateService.instant('MERCHANT.LOGIN.TOAST_MESSAGE.LOGIN_SUCCESS_HEADLINE')
      );
    },
      () => this.loginFailed = true
    );
  }
}
