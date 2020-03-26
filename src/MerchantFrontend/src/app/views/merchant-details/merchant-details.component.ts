import { Component, OnInit } from '@angular/core';
import { StateService } from '../../core/state/state.service';
import { Merchant } from '../../core/models/merchant.model';
import { LoginService } from '../../core/services/login.service';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';

@Component({
  selector: 'portal-merchant-details',
  templateUrl: './merchant-details.component.html',
  styleUrls: ['./merchant-details.component.scss']
})
export class MerchantDetailsComponent implements OnInit {

  merchant$: Observable<Merchant|null>;

  constructor(
    private loginService: LoginService,
    private stateService: StateService,
    private router: Router
  ) { }

  ngOnInit(): void {
    this.merchant$ = this.stateService.getMerchant();
  }

  logout(): void {
    this.loginService.logout();
    this.router.navigate(['login', 'merchant']);
  }
}
