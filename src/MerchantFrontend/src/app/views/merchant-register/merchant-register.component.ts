import { Component, Input, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PasswordValidators } from '../../shared/validators/password.validator';
import { Merchant, MerchantRegistration } from '../../core/models/merchant.model';
import { ToastService } from '../../core/services/toast.service';
import { TranslateService } from '@ngx-translate/core';
import { OrganizationAuthority } from '../../core/models/organization.model';

@Component({
  selector: 'portal-merchant-register',
  templateUrl: './merchant-register.component.html',
  styleUrls: ['./merchant-register.component.scss']
})
export class MerchantRegisterComponent implements OnInit {

  authorities$: Observable<OrganizationAuthority[]>;
  registerForm: FormGroup;
  registrationFinished = false;
  showDuplicateMailError = false;
  private initialRegisterFormValues: any;

  @Input() registerModalOpen = true;

  constructor(
    private readonly merchantApiService: MerchantApiService,
    private readonly formBuilder: FormBuilder,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
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
      policy: [false, Validators.requiredTrue],
      tos: [false, Validators.requiredTrue]
    }, {validator: Validators.compose([PasswordValidators.matchPassword])});
    this.initialRegisterFormValues = this.registerForm.value;
  }

  register(): void {
    const merchant: MerchantRegistration = {
      publicCompanyName: this.registerForm.get('name').value,
      email: this.registerForm.get('mail').value,
      password: this.registerForm.get('password').value,
      salesChannelId: this.registerForm.get('authority').value.id
    };

    this.merchantApiService.registerMerchant(merchant).subscribe(
      () => {
        this.registerModalOpen = false;
        this.registrationFinished = true;
        this.toastService.success(
          this.translateService.instant('MERCHANT.REGISTER.TOAST_MESSAGES.SUCCESS_HEADLINE')
        );
        },
      (error) => {
        if("MERCHANT_EMAIL_ALREADY_EXISTS" === error.error.errors[0].code) {
          this.showDuplicateMailError = true;
        }
        this.toastService.error(
          this.translateService.instant('MERCHANT.REGISTER.TOAST_MESSAGES.ERROR_HEADLINE')
        );
      }
    );
  }

  registerModalClosed() {
    this.registerForm.reset(this.initialRegisterFormValues);
    this.registerModalOpen = false;
  }
}
