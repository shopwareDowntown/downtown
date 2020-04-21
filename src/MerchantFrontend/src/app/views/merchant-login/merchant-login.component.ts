import { Component, Input, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { LoginService } from '../../core/services/login.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { ToastService } from '../../core/services/toast.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'portal-merchant-login',
  templateUrl: './merchant-login.component.html',
  styleUrls: ['./merchant-login.component.scss']
})
export class MerchantLoginComponent implements OnInit {
  isLogging: boolean;
  loginFailed = false;
  @Input() loginModalOpen: boolean;
  @Input() registrationCompleted = false;
  loginForm: FormGroup;
  passwordResetForm: FormGroup;
  passwordResetMode = false;
  private initialFormState: any;
  private initialResetFormValues: any;

  constructor(
    private readonly router: Router,
    private readonly loginService: LoginService,
    private readonly formBuilder: FormBuilder,
    private readonly merchantApiService: MerchantApiService,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) {}

  ngOnInit(): void {
    this.initializeForm();
    this.resetForm();
  }

  enterLogin($event: KeyboardEvent) {
    // listen for enter key to login
    if ($event.code === 'Enter') {
      this.doLogin();
    }
  }

  public doLogin() {
    this.loginFailed = false;
    this.loginService
      .merchantLogin(
        this.loginForm.get('username').value,
        this.loginForm.get('password').value
      )
      .subscribe(
        result => {
          this.modalClosed();
          this.router.navigate(['/merchant/home']);
          this.toastService.success(
            this.translateService.instant('MERCHANT.LOGIN.TOAST_MESSAGE.LOGIN_SUCCESS_HEADLINE')
          );
        },
        () => {
          this.loginFailed = true;
        }
      );
  }

  doPasswordReset() {
    this.merchantApiService
      .resetMerchantPassword(this.passwordResetForm.value)
      .subscribe(() => {
        this.passwordResetForm.reset(this.initialResetFormValues);
        this.toastService.success(
          this.translateService.instant('MERCHANT.LOGIN.TOAST_MESSAGE.PASSWORD_RESET_SUCCESS_HEADLINE')
        );
        this.passwordResetMode = false;
        this.modalClosed();
      });
  }

  initializeForm(): void {
    this.loginForm = this.formBuilder.group({
      username: ['', Validators.required],
      password: ['', Validators.required]
    });
    this.initialFormState = this.loginForm.value;
  }

  modalClosed(): void {
    this.loginForm.reset();
    this.loginModalOpen = false;
  }

  private resetForm() {
    this.passwordResetForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]]
    });
    this.initialResetFormValues = this.passwordResetForm.value;
  }
}
