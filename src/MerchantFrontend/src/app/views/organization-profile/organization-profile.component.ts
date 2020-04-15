import { Component, OnInit } from '@angular/core';
import { StateService } from '../../core/state/state.service';
import { Organization } from '../../core/models/organization.model';
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {MerchantApiService} from "../../core/services/merchant-api.service";
import {ToastService} from "../../core/services/toast.service";
import {TranslateService} from "@ngx-translate/core";
import {switchMap} from "rxjs/operators";
import {of} from "rxjs";

@Component({
  selector: 'portal-organization-profile',
  templateUrl: './organization-profile.component.html',
  styleUrls: ['./organization-profile.component.scss']
})
export class OrganizationProfileComponent implements OnInit {

  organization: Organization;
  profileForm: FormGroup;

  constructor(
    private readonly stateService: StateService,
    private readonly formBuilder: FormBuilder,
    private readonly merchantApiService: MerchantApiService,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) { }

  ngOnInit(): void {
    this.stateService.getOrganization().subscribe((organization: Organization) => {
      this.organization = organization;
      this.initializeForm();
    })
  }

  updateOrganization(): void {
    const updateData = {
      name: this.profileForm.get('name').value,
      homeText: this.profileForm.get('homeText').value,
      firstName: this.profileForm.get('firstName').value,
      lastName: this.profileForm.get('lastName').value,
      phone: this.profileForm.get('phone').value,
      postCode: this.profileForm.get('postCode').value,
      city: this.profileForm.get('city').value,
      imprint: this.profileForm.get('imprint').value,
      tos: this.profileForm.get('tos').value,
      privacy: this.profileForm.get('privacy').value
    } as Organization;
    this.merchantApiService.updateOrganization(updateData).pipe(
      switchMap((organization: Organization) => {
        if (null !== this.profileForm.get('logo').value) {
          return this.merchantApiService.setOrganizationLogo(this.profileForm.get('logo').value);
        }
        return of(organization);
      }),
      switchMap((result: {successful: boolean}|Organization) => {
        if (null !== this.profileForm.get('homeHeroImage').value) {
          return this.merchantApiService.setOrganizationHomeImage(this.profileForm.get('homeHeroImage').value);
        }
        return of(result)
      }),
      switchMap((result: any|Organization) => {
        if (!result.id) {
          return this.merchantApiService.getOrganization();
        }
        return of(result);
      })
    ).subscribe((organization: Organization) => {
      this.stateService.setOrganization(organization);
      this.organization = organization;
      this.toastService.success(
        this.translateService.instant('ORGANIZATION.PROFILE.TOAST_MESSAGES.UPDATE_ORGANIZATION_SUCCESS_HEADLINE')
      );
    }, () => this.toastService.error(
      this.translateService.instant('ORGANIZATION.PROFILE.TOAST_MESSAGES.UPDATE_ORGANIZATION_ERROR_HEADLINE'),
      this.translateService.instant('ORGANIZATION.PROFILE.TOAST_MESSAGES.UPDATE_ORGANIZATION_ERROR_TEXT')
    ));
  }

  logoSelected(logo: File): void {
    this.profileForm.get('logo').setValue(logo);
  }

  imageSelected(image: File): void {
    this.profileForm.get('homeHeroImage').setValue(image);
  }

  private initializeForm(): void {
    this.profileForm = this.formBuilder.group({
      name: [this.organization.name],
      homeText: [this.organization.homeText],
      firstName: [this.organization.firstName, Validators.required],
      lastName: [this.organization.lastName, Validators.required],
      phone: [this.organization.phone],
      postCode: [this.organization.postCode],
      city: [this.organization.city],
      description: [''],
      imprint: [this.organization.imprint],
      tos: [this.organization.tos],
      privacy: [this.organization.privacy],
      logo: [null],
      homeHeroImage: [null]
    })
  }
}
