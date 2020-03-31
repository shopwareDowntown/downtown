import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Authority } from 'src/app/core/models/authority.model';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'portal-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit{
  showRegisterOrganizationModal: boolean;
  showRegisterMerchantModal: boolean;
  authorities$: Observable<Authority[]>;
  registrationCompleted = false;

  constructor(
    private readonly merchantApiService: MerchantApiService,
    private readonly route: ActivatedRoute
  ) {}


  ngOnInit(): void {
    this.authorities$ = this.getAuthorities();
    this.registrationCompleted = this.route.snapshot.queryParamMap.get("merchantRegistrationCompleted") === '1';
    if (this.registrationCompleted) {

    }
  }

  getAuthorities(): Observable<Authority[]>{
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
