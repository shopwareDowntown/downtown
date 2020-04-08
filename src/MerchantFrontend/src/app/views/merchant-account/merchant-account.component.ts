import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Merchant } from '../../core/models/merchant.model';
import { StateService } from '../../core/state/state.service';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { ToastService } from '../../core/services/toast.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'portal-merchant-account',
  templateUrl: './merchant-account.component.html',
  styleUrls: ['./merchant-account.component.scss']
})
export class MerchantAccountComponent implements OnInit {
  form: FormGroup;
  merchant: Merchant;
  changePasswordModalOpen = false;
  changeEmailModalOpen = false;

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly stateService: StateService,
    private readonly merchantApiService: MerchantApiService,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) {}

  ngOnInit(): void {
    this.stateService.getMerchant().subscribe((merchant: Merchant) => {
      this.merchant = merchant;
    }, () => {
      this.toastService.error(
        this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.MERCHANT_LOAD_ERROR_HEADLINE')
      );
    });

    this.form = this.formBuilder.group({
      firstName: [this.merchant.firstName, [Validators.required]],
      lastName: [this.merchant.lastName, [Validators.required]],
      currentEmail: [
        {value: this.merchant.email, disabled: true},
        [Validators.required, Validators.email]
      ]
    });
  }

  saveChanges(): void {
    this.merchant.firstName = this.form.value.firstName;
    this.merchant.lastName = this.form.value.lastName;

    const updateData = {
      firstName: this.form.value.firstName,
      lastName: this.form.value.lastName
    } as Merchant;

    this.merchantApiService.updateMerchant(updateData).subscribe((merchant: Merchant) => {
      this.merchant = merchant;
      this.stateService.setMerchant(merchant);
      this.toastService.success(
        this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.UPDATE_MERCHANT_SUCCESS_HEADLINE')
      );
    }, () => {
      this.toastService.error(
        this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.UPDATE_MERCHANT_ERROR_HEADLINE'),
        this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.UPDATE_MERCHANT_ERROR_TEXT')
      );
    });
  }

  openChangePassword() {
    this.changePasswordModalOpen = false;
    setTimeout(() => {
      this.changePasswordModalOpen = true;
    });
  }

  openChangeEmail() {
    this.changeEmailModalOpen = false;
    setTimeout(() => {
      this.changeEmailModalOpen = true;
    });
  }

  passwordChanged(newPassword: string) {
    const updatedData = {
      password: newPassword,
      firstName: this.merchant.firstName,
      lastName: this.merchant.lastName
    } as Merchant;
    this.merchant.password = newPassword;
    this.merchantApiService
      .updateMerchant(updatedData)
      .subscribe((merchant: Merchant) => {
        this.merchant = merchant;
        this.toastService.success('Passwort erfolgreich geändert');
      }, () => {
        this.toastService.error('Fehler beim Speichern');
      });
  }

  emailChanged(newEmail: string) {
    const updatedData = {
      email: newEmail,
      firstName: this.merchant.firstName,
      lastName: this.merchant.lastName
    } as Merchant;
    this.merchantApiService
      .updateMerchant(updatedData)
      .subscribe((merchant: Merchant) => {
        this.merchant = merchant;
        this.form.get('currentEmail').setValue(merchant.email);
        this.toastService.success('E-Mail erfolgreich geändert');
      }, () => {
        this.toastService.error('Fehler beim Speichern');
      });
  }
}
