import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Merchant } from '../../core/models/merchant.model';
import { StateService } from '../../core/state/state.service';
import { MerchantApiService } from '../../core/services/merchant-api.service';

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
    private readonly merchantApiService: MerchantApiService
  ) { }

  ngOnInit(): void {
    this.stateService.getMerchant().subscribe((merchant: Merchant) => {
      this.merchant = merchant;
    });

    this.form = this.formBuilder.group({
      firstName: [this.merchant.firstName, [Validators.required]],
      lastName: [this.merchant.lastName, [Validators.required]],
      currentEmail: [this.merchant.email, [Validators.required, Validators.email]]
    });
  }

  saveChanges(): void {
    this.merchant.firstName = this.form.value.firstName;
    this.merchant.lastName = this.form.value.lastName;

    const updateData = {
      firstName: this.form.value.firstName,
      lastName: this.form.value.lastName
    } as Merchant

    this.merchantApiService.updateMerchant(updateData).subscribe((merchant: Merchant) => {
      this.merchant = merchant;
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
    this.merchantApiService.updateMerchant(updatedData).subscribe((merchant: Merchant) => {
      this.merchant = merchant;
    });
  }

  emailChanged(newEmail: string) {
    const updatedData = {
      email: newEmail,
      firstName: this.merchant.firstName,
      lastName: this.merchant.lastName
    } as Merchant;
    this.merchantApiService.updateMerchant(updatedData).subscribe((merchant: Merchant) => {
      this.merchant = merchant;
      this.form.get('currentEmail').setValue(merchant.email);
    });
  }
}
