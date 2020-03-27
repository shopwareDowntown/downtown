import { Component } from '@angular/core';
import { Observable } from 'rxjs';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Authority } from 'src/app/core/models/authority.model';

@Component({
  selector: 'portal-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent {
  showRegisterOrganizationModal: boolean;
  showRegisterMerchantModal: boolean;
  authorities$: Observable<Authority[]>;
  formBuilder: any;

  constructor(private merchantApiService: MerchantApiService) {}

  getAuthorities(): void {
    this.authorities$ = this.merchantApiService.getAuthorities();
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
