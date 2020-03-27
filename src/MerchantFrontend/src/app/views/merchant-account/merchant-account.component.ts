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
      currentEmail: [this.merchant.email, [Validators.required, Validators.email]],
      newEmail: ['', [Validators.email]],
      newEmailConfirmation: ['', [Validators.email]],
      newPassword: ['', [Validators.minLength(8)]],
      newPasswordConfirmation: ['', [Validators.minLength(8)]]
    }, {
      validator: [this.checkPasswords, this.checkEmail],
    });
  }

  resetChanges(): void {
    this.form.get('firstName').patchValue(this.merchant.firstName);
    this.form.get('lastName').patchValue(this.merchant.lastName);
    this.form.get('newEmail').patchValue('');
    this.form.get('newEmailConfirmation').patchValue('');
    this.form.get('newPassword').patchValue('');
    this.form.get('newPasswordConfirmation').patchValue('');
    this.form.markAsPristine();
  }

  saveChanges(): void {
    this.merchant.firstName = this.form.value.firstName;
    this.merchant.lastName = this.form.value.lastName;
    this.merchant.email = this.form.value.email;
    this.merchant.password = this.form.value.newPassword;

    this.merchantApiService.updateMerchant(this.merchant).subscribe((merchant: Merchant) => {
      this.merchant = merchant;
    });
  }

  private checkPasswords(group: FormGroup) {
    const password = group.get('newPassword').value;
    const passwordConfirmation = group.get('newPasswordConfirmation').value;

    return password === passwordConfirmation ? null : { notSame: true }
  }

  private checkEmail(group: FormGroup) {
    let email = group.get('newEmail').value;
    let emailConfirmation = group.get('newEmailConfirmation').value;

    return email === emailConfirmation ? null : { notSame: true }
  }
}
