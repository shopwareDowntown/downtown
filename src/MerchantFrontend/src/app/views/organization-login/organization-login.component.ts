import {Component, EventEmitter, OnInit, Output} from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LoginService } from '../../core/services/login.service';
import { Router } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { ToastService } from '../../core/services/toast.service';
import {MerchantApiService} from "../../core/services/merchant-api.service";

@Component({
  selector: 'portal-organization-login',
  templateUrl: './organization-login.component.html',
  styleUrls: ['./organization-login.component.scss']
})
export class OrganizationLoginComponent implements OnInit{
  loginForm: FormGroup;
  isLogging = false;
  loginFailed = false;
  passwordResetMode = false;
  passwordResetForm: FormGroup;

  private initialFormState: any;
  private initialResetFormValues: any;

  @Output() modalClosed = new EventEmitter<void >();

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly loginService: LoginService,
    private readonly router: Router,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService,
    private readonly merchantApiService: MerchantApiService
  ) {}

  ngOnInit(): void {
    this.loginForm = this.formBuilder.group({
      username: ['', Validators.required],
      password: ['', Validators.required]
    });
    this.initialFormState = this.loginForm.value;
    this.initializeResetForm()
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

  doPasswordReset() {
    this.merchantApiService
      .resetOrganizationPassword(this.passwordResetForm.value)
      .subscribe(() => {
        this.passwordResetForm.reset(this.initialResetFormValues);
        this.toastService.success(
          this.translateService.instant('MERCHANT.LOGIN.TOAST_MESSAGE.PASSWORD_RESET_SUCCESS_HEADLINE')
        );
        this.passwordResetMode = false;
        this.modalClosed.emit();
      });
  }

  private initializeResetForm() {
    this.passwordResetForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]]
    });
    this.initialResetFormValues = this.passwordResetForm.value;
  }
}
