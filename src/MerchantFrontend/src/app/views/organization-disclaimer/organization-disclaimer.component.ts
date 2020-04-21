import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Organization } from '../../core/models/organization.model';
import { StateService } from '../../core/state/state.service';
import { switchMap, take } from 'rxjs/operators';
import { of } from 'rxjs';
import { ToastService } from '../../core/services/toast.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'portal-organization-disclaimer',
  templateUrl: './organization-disclaimer.component.html',
  styleUrls: ['./organization-disclaimer.component.scss']
})
export class OrganizationDisclaimerComponent implements OnInit {

  form: FormGroup;
  organization: Organization;

  constructor(
    private readonly formBuilder: FormBuilder,
    private readonly merchantApiService: MerchantApiService,
    private readonly stateService: StateService,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) { }

  ngOnInit(): void {
    this.stateService.getOrganization().pipe(
      take(1)
    ).subscribe((organization: Organization) => {
      this.organization = organization;
      this.initializeForm();
    });
  }

  imageSelected(image: File): void {
    this.form.get('image').setValue(image);
  }

  updateDisclaimer(): void {
    const updateData = {
      disclaimer: {
        active: this.form.get('active').value,
        text: this.form.get('text').value
      }
    }
    this.merchantApiService.updateOrganization(updateData).pipe(
      switchMap((organization: Organization) => {
        if (null !== this.form.get('image').value) {
          return this.merchantApiService.setDisclaimerImage(this.form.get('image').value);
        }
        return of(organization);
      }),
      switchMap((result: any|Organization) => {
        if (!result.id) {
          return this.merchantApiService.getOrganization();
        }
        return of(result);
      })
    ).subscribe((organization: Organization) => {
      this.organization = organization;
      this.stateService.setOrganization(this.organization);
      this.toastService.success(
        this.translateService.instant('ORGANIZATION.DISCLAIMER.TOAST_MESSAGES.UPDATE_SUCCESSFUL_HEADLINE')
      );
    }, () => {
      this.toastService.error(
        this.translateService.instant('ORGANIZATION.DISCLAIMER.TOAST_MESSAGES.UPDATE_ERROR_HEADLINE'),
        this.translateService.instant('ORGANIZATION.DISCLAIMER.TOAST_MESSAGES.UPDATE_ERROR_TEXT')
      );
    });
  }

  removeImage() {
    this.merchantApiService.removeDisclaimerImage().pipe(
      switchMap(() => {
        return this.merchantApiService.getOrganization();
      })
    ).subscribe((organization: Organization) => {
      this.organization = organization;
      this.stateService.setOrganization(organization);
      this.toastService.success(
        this.translateService.instant('ORGANIZATION.DISCLAIMER.TOAST_MESSAGES.DELETE_IMAGE_SUCCESS_HEADLINE')
      );
    }, () => {
      this.toastService.error(
        this.translateService.instant('ORGANIZATION.DISCLAIMER.TOAST_MESSAGES.DELETE_IMAGE_ERROR_HEADLINE'),
        this.translateService.instant('ORGANIZATION.DISCLAIMER.TOAST_MESSAGES.DELETE_IMAGE_ERROR_TEXT')
      );
    });
  }

  private initializeForm() {
    this.form = this.formBuilder.group({
      active: [this.organization.disclaimer?.active],
      text: [this.organization.disclaimer?.text],
      image: [null]
    });

    this.handleActivationDisabled(this.form.get('text').value);

    this.form.get('text').valueChanges.subscribe((value: string) => {
      this.handleActivationDisabled(value);
    })
  }

  private handleActivationDisabled(textValue: string): void {
    if (textValue === '' || !textValue) {
      this.form.get('active').setValue(false);
      this.form.get('active').disable();
    } else {
      this.form.get('active').enable();
    }
  }
}

