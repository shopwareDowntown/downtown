import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordValidators } from '../../../shared/validators/password.validator';
import { MerchantApiService } from '../../../core/services/merchant-api.service';

@Component({
  selector: 'portal-password-reset-modal',
  templateUrl: './password-reset-modal.component.html',
  styleUrls: ['./password-reset-modal.component.scss']
})
export class PasswordResetModalComponent implements OnInit {

  form: FormGroup;

  @Input() modalOpen = false;
  @Input() token: string;

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly merchantApiService: MerchantApiService
  ) { }

  ngOnInit(): void {
    this.form = this.formBuilder.group({
      password: ['', [Validators.required, Validators.minLength(8)]],
      repeatPassword: ['', [Validators.required, Validators.minLength(8)]]
    }, {validator: Validators.compose([PasswordValidators.matchPassword])});
  }

  doReset(): void {
    this.merchantApiService.resetPasswordConfirm(this.form.get('password').value, this.token).subscribe(() => {
      this.modalOpen = false;
    });
    //TODO: what should happen on errors?
  }
}
