import { Component, Input, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { Authority } from '../../core/models/authority.model';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordValidators } from '../../shared/validators/password.validator';
import { Merchant, MerchantRegistration } from '../../core/models/merchant.model';

@Component({
  selector: 'portal-merchant-register',
  templateUrl: './merchant-register.component.html',
  styleUrls: ['./merchant-register.component.scss']
})
export class MerchantRegisterComponent implements OnInit {

  authorities$: Observable<Authority[]>;

  registerForm: FormGroup;
  registrationFinished = false;
  @Input() registerModalOpen = true;

  constructor(
    private readonly merchantApiService: MerchantApiService,
    private readonly formBuilder: FormBuilder,
  ) { }

  ngOnInit(): void {
    this.authorities$ = this.merchantApiService.getAuthorities();
    this.initializeForm();
  }

  initializeForm(): void {
    this.registerForm = this.formBuilder.group({
      name: ['', Validators.required],
      mail: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      repeatPassword: ['', [Validators.required, Validators.minLength(8)]],
      authority: [ null, Validators.required],
      policy: [false, Validators.requiredTrue]
    }, {validator: Validators.compose([PasswordValidators.matchPassword])});
  }

  register(): void {
    const merchant: MerchantRegistration = {
      publicCompanyName: this.registerForm.get('name').value,
      email: this.registerForm.get('mail').value,
      password: this.registerForm.get('password').value,
      salesChannelId: this.registerForm.get('authority').value.id
    };

    this.merchantApiService.registerMerchant(merchant, this.registerForm.get('authority').value.accessKey).subscribe(
      () => {
        this.registerModalOpen = false;
        this.registrationFinished=true;
        },
      () => {}
    );
  }

  registerModalClosed() {
    this.registerModalOpen = false;
  }
}
