import { Component } from '@angular/core';
import { MerchantApiService } from '../../core/services/merchant-api.service';
import { Observable } from 'rxjs';
import { Authority } from 'src/app/core/models/authority.model';

@Component({
  selector: 'portal-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent {

  constructor(private merchantApiService: MerchantApiService) {}

  getAuthorities(): Observable<Authority[]> {
    return this.merchantApiService.getAuthorities();
  }
}
