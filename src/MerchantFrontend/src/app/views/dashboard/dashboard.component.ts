import {Component, OnInit} from '@angular/core';
import {Observable} from 'rxjs';
import {MerchantApiService} from '../../core/services/merchant-api.service';

import {ActivatedRoute} from '@angular/router';
import {OrganizationAuthority} from '../../core/models/organization.model';
import {Role} from "../../core/state/state.service";

@Component({
  selector: 'portal-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit{
  showRegisterOrganizationModal: boolean;
  showRegisterMerchantModal: boolean;
  showPasswordResetConfirmModal = false;
  authorities$: Observable<OrganizationAuthority[]>;
  registrationCompleted = false;
  token: string;
  passwordResetForRole: Role;

  constructor(
    private readonly merchantApiService: MerchantApiService,
    private readonly activeRoute: ActivatedRoute
  ) {}


  ngOnInit(): void {
    this.activeRoute.params.subscribe((params) => {
      if (params.tokenMerchant) {
        this.token = params.tokenMerchant;
        this.passwordResetForRole = Role.merchant;
        this.showPasswordResetConfirmModal = true;
      }
      if (params.tokenOrganization) {
        this.token = params.tokenOrganization;
        this.passwordResetForRole = Role.organization;
        this.showPasswordResetConfirmModal = true;
      }
    });
    this.authorities$ = this.getAuthorities();
    this.registrationCompleted = this.activeRoute.snapshot.queryParamMap.get("merchantRegistrationCompleted") === '1';

  }

  getAuthorities(): Observable<OrganizationAuthority[]>{
    return this.authorities$ = this.merchantApiService.getAuthorities();
  }

  registerOrganization(): void {
    this.showRegisterOrganizationModal = false;
    setTimeout(() => {
      this.showRegisterOrganizationModal = true;
    });
  }

  registerMerchant(): void {
    this.showRegisterMerchantModal = false;
    setTimeout(() => {
      this.showRegisterMerchantModal = true;
    });
  }
}
