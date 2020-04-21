import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordValidators } from '../../../shared/validators/password.validator';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import {Role} from "../../../core/state/state.service";
import {ToastService} from "../../../core/services/toast.service";
import {TranslateService} from "@ngx-translate/core";

@Component({
  selector: 'portal-password-reset-modal',
  templateUrl: './password-reset-modal.component.html',
  styleUrls: ['./password-reset-modal.component.scss']
})
export class PasswordResetModalComponent implements OnInit {

  form: FormGroup;

  @Input() modalOpen = false;
  @Input() token: string;
  @Input() role: Role;

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly merchantApiService: MerchantApiService,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) { }

  ngOnInit(): void {
    this.form = this.formBuilder.group({
      password: ['', [Validators.required, Validators.minLength(8)]],
      repeatPassword: ['', [Validators.required, Validators.minLength(8)]]
    }, {validator: Validators.compose([PasswordValidators.matchPassword])});
  }

  doReset(): void {
    const password = this.form.get('password').value;
    let reset$ = null;
    if (this.role === Role.merchant) {
      reset$ = this.merchantApiService.resetMerchantPasswordConfirm(password , this.token);
    }
    if (this.role === Role.organization) {
      reset$ = this.merchantApiService.resetOrganizationPasswordConfirm(password, this.token)
    }
    if (reset$ === null) {
      return;
    }
    reset$.subscribe(() => {
      this.modalOpen = false;
      this.toastService.success(
        this.translateService.instant('DASHBOARD.PASSWORD_RESET.TOAST_MESSAGES.SUCCESS_HEADLINE')
      );
    }, () => {
      this.toastService.error(
        this.translateService.instant('DASHBOARD.PASSWORD_RESET.TOAST_MESSAGES.SUCCESS_HEADLINE'),
        this.translateService.instant('DASHBOARD.PASSWORD_RESET.TOAST_MESSAGES.ERROR_TEXT')
      )
    });
  }
}
