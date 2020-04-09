import { Component, OnInit } from '@angular/core';
import { StateService } from '../../core/state/state.service';
import { Merchant } from '../../core/models/merchant.model';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Category } from '../../core/models/category.model';
import { Country } from '../../core/models/country.model';
import { ToastService } from '../../core/services/toast.service';
import { switchMap } from 'rxjs/operators';
import { merge, of} from 'rxjs';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'portal-merchant-details',
  templateUrl: './merchant-details.component.html',
  styleUrls: ['./merchant-details.component.scss']
})
export class MerchantDetailsComponent implements OnInit {
  merchant: Merchant;
  profileForm: FormGroup;
  merchantLoaded = false;
  categoriesLoaded = false;

  categories: Category[];

  constructor(
    private readonly stateService: StateService,
    private readonly formBuilder: FormBuilder,
    private readonly merchantApiService: MerchantApiService,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) {}

  countries: Country[] = [];

  ngOnInit(): void {
    this.stateService.getMerchant().subscribe((merchant: Merchant) => {
      if(!merchant) {
        return;
      }
      this.merchant = merchant;
      this.merchantLoaded = true;
      this.createForm();
    }, () => {
      this.toastService.error(
        this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.MERCHANT_LOAD_ERROR_HEADLINE')
      );
    });

    this.merchantApiService
      .getCategories()
      .subscribe((categories: Category[]) => {
        this.categories = categories;
        this.categoriesLoaded = true;
      });

    this.merchantApiService
      .getCountries()
      .subscribe((countries: { data: Country[] }) => {
        this.countries = countries.data;
      });
  }

  save() {
    const newData = this.profileForm.getRawValue();
    // update data
    const updatedData = {
      publicCompanyName: newData.publicCompanyName,
      publicOwner: newData.publicOwner,
      publicPhoneNumber: newData.publicPhoneNumber,
      publicEmail: newData.publicEmail,
      publicWebsite: newData.publicWebsite,
      categoryId: newData.categoryId,
      publicOpeningTimes: newData.publicOpeningTimes,
      publicDescription: newData.publicDescription,
      public: newData.public,
      street: newData.street,
      zip: newData.zip,
      city: newData.city,
      countryId: newData.countryId,
      imprint: newData.imprint,
      tos: newData.tos,
      revocation: newData.revocation,
      privacy: newData.privacy
    } as Merchant;

    this.merchantApiService.updateMerchant(updatedData).pipe(
      switchMap((merchant: Merchant) => {
        if (this.profileForm.get('cover').value !== null) {
          if (this.merchant.cover !== null) {
            return this.merchantApiService.deleteMerchantCoverImage(this.merchant.cover.id);
          }
          return of([])
        }
        return of(merchant);
      }),
      switchMap((result: [] | Merchant) => {
        if (Array.isArray(result)) {
          return this.merchantApiService.addCoverToMerchant(this.profileForm.get('cover').value);
        }
        return of(result);
      }),
      switchMap((result: true | Merchant) => {
        if (result === true) {
          return this.merchantApiService.getMerchant()
        }
        return  of(result);
      })
    ).subscribe((merchant: Merchant) => {
      this.merchant = merchant;
      this.stateService.setMerchant(merchant);
      this.toastService.success(
        this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.UPDATE_MERCHANT_SUCCESS_HEADLINE')
      );
    },
      () => {
        this.toastService.error(
          this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.UPDATE_MERCHANT_ERROR_HEADLINE'),
          this.translateService.instant('MERCHANT.DETAILS.TOAST_MESSAGES.UPDATE_MERCHANT_ERROR_TEXT')
        );
      });
  }

  private createForm() {
    this.profileForm = this.formBuilder.group({
      public: this.merchant.public,
      publicCompanyName: [this.merchant.publicCompanyName, Validators.required],
      publicOwner: [this.merchant.publicOwner],
      street: [this.merchant.street],
      zip: [this.merchant.zip, Validators.required],
      city: [this.merchant.city, Validators.required],
      countryId: [this.merchant.countryId, Validators.required],
      categoryId: [this.merchant.categoryId, Validators.required],
      publicPhoneNumber: [this.merchant.publicPhoneNumber],
      publicEmail: [this.merchant.publicEmail],
      publicWebsite: this.merchant.publicWebsite,
      publicOpeningTimes: [this.merchant.publicOpeningTimes],
      publicDescription: this.merchant.publicDescription,
      cover: [null],
      imprint: [this.merchant.imprint],
      tos: [this.merchant.tos],
      privacy: [this.merchant.privacy],
      revocation: [this.merchant.revocation],
    });
    if (false === this.isAllowedToActivate()) {
      this.profileForm.get('public').setValue(false);
      this.profileForm.get('public').disable();
    }

    const imprintChanges$ = this.profileForm.get('imprint').valueChanges;
    const tosChanges$ = this.profileForm.get('tos').valueChanges;
    const privacyChanges$ = this.profileForm.get('privacy').valueChanges;
    const revocationChanges$ = this.profileForm.get('revocation').valueChanges;
    merge(imprintChanges$, tosChanges$, privacyChanges$, revocationChanges$).subscribe(() => {
      if (false === this.isAllowedToActivate()) {
        this.profileForm.get('public').setValue(false);
        this.profileForm.get('public').disable();
      } else {
        this.profileForm.get('public').enable();
      }
    });
  }

  imageSelected(value: File) {
    this.profileForm.get('cover').setValue(value);
  }

  private isAllowedToActivate() {
    if (
      !this.profileForm.get('imprint').value
      || !this.profileForm.get('tos').value
      || !this.profileForm.get('revocation').value
      || !this.profileForm.get('privacy').value
    ) {
      return false;
    }
    return true;
  }
}
