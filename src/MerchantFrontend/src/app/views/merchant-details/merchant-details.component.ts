import { Component, OnInit } from '@angular/core';
import { StateService } from '../../core/state/state.service';
import { Merchant } from '../../core/models/merchant.model';
import { LoginService } from '../../core/services/login.service';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Category } from '../../core/models/category.model';

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
    private loginService: LoginService,
    private stateService: StateService,
    private router: Router,
    private formBuilder: FormBuilder,
    private merchantApiService: MerchantApiService
  ) {}

  mockCountries = [
    {id: 'germany', label: 'Deutschland'},
    {id: 'alsoGermany', label: 'Auch Deutschland'},
    {id: 'schöppingen', label: 'Schöppingen'},
    {id: 'bielefeld', label: 'Bielefeld'}
  ];

  ngOnInit(): void {

    this.stateService.getMerchant().subscribe((merchant: Merchant | null) => {
      if (merchant !== null) {
        this.merchant = merchant;
        this.merchantLoaded = true;
        this.createForm();
      }
    });

    this.merchantApiService.getCategories().subscribe((categories: Category[]) => {
      this.categories = categories;
      this.categoriesLoaded = true;
    });
  }

  save() {
    const newData = this.profileForm.getRawValue();

    // update data
    const updatedData = {
      publicCompanyName: newData.publicCompanyName,
      owner: newData.owner,
      publicPhoneNumber: newData.publicPhoneNumber,
      publicEmail: newData.publicEmail,
      publicWebsite: newData.publicWebsite,
      categoryId: newData.categoryId,
      publicOpeningTimes: newData.publicOpeningTimes,
      publicDescription: newData.publicDescription,
      public: newData.public,
      lastName: this.merchant.lastName,
      firstName: this.merchant.firstName,
      street: newData.street,
      zip: newData.zip,
      city: newData.city,
      country: newData.country,
      email: this.merchant.email,
      password: this.merchant.password
    } as Merchant;

    this.merchantApiService.updateMerchant(updatedData)
      .subscribe((merchant: Merchant) => {
        this.merchant = merchant;
      });
  }

  private createForm() {
    this.profileForm = this.formBuilder.group({
      public: this.merchant.public,
      publicCompanyName: [this.merchant.publicCompanyName, Validators.required],
      owner: [this.merchant.owner, Validators.required],
      street: [this.merchant.street, Validators.required],
      zip: [this.merchant.zip, Validators.required],
      city: [this.merchant.city, Validators.required],
      country: [this.merchant.country, Validators.required],
      publicPhoneNumber: [this.merchant.publicPhoneNumber, Validators.required],
      publicEmail: [this.merchant.publicEmail, [Validators.required, Validators.email]],
      publicWebsite: this.merchant.publicWebsite,
      categoryId: [this.merchant.categoryId, Validators.required],
      publicOpeningTimes: [this.merchant.publicOpeningTimes, Validators.required],
      publicDescription: this.merchant.publicDescription,
    });
  }
}
