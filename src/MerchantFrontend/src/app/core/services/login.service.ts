import { Injectable } from '@angular/core';
import { Observable, of, throwError } from 'rxjs';
import { switchMap, tap } from 'rxjs/operators';
import { Merchant, MerchantLoginResult } from '../models/merchant.model';
import { Role, StateService } from '../state/state.service';
import { MerchantApiService } from './merchant-api.service';
import { LocalStorageService } from './local-storage.service';
import { Organization, OrganizationLoginResult } from '../models/organization.model';

@Injectable({
  providedIn: 'root'
})
export class LoginService {

  constructor(
    private readonly merchantApiService: MerchantApiService,
    private readonly stateService: StateService,
    private readonly localStorageService: LocalStorageService,
  ) {
  }

  merchantLogin(username: string, password: string): Observable<Merchant> {
    return this.merchantApiService.loginMerchant(username, password)
      .pipe(
        tap((result: MerchantLoginResult) => {
          this.stateService.setSwContextToken(result['sw-context-token']);
          this.localStorageService.setItem('sw-context-token', result['sw-context-token']);
          this.localStorageService.setItem('role', Role.merchant);
        }),
        switchMap((result: MerchantLoginResult) => {
          return this.merchantApiService.getMerchant();
        }),
        tap((merchant: Merchant) => {
          this.stateService.setMerchant(merchant);
        }),
      );
  }

  organizationLogin(username: string, password: string) {
    return this.merchantApiService.loginOrganization(username, password)
      .pipe(
        tap((result: OrganizationLoginResult) => {
          this.stateService.setSwContextToken(result['sw-context-token']);
          this.localStorageService.setItem('sw-context-token', result['sw-context-token']);
          this.localStorageService.setItem('role', Role.organization);
        }),
        switchMap((result: OrganizationLoginResult) => {
          return this.merchantApiService.getOrganization();
        }),
        tap((organization: Organization) => {
          this.stateService.setOrganization(organization);
        })
      )
  }

  loginWithToken(token: string, role: string): Observable<Merchant| Organization> {
    this.stateService.setSwContextToken(token);
    if (role === Role.merchant) {
      return this.merchantApiService.getMerchant()
        .pipe(
          tap((merchant: Merchant) => {
            this.stateService.setMerchant(merchant);
          })
        );
    }
    if (role === Role.organization) {
      return this.merchantApiService.getOrganization()
        .pipe(
          tap((organization: Organization) => {
            this.stateService.setOrganization(organization);
          })
        )
    }
    throwError('not logged in correctly');
  }

  logout() {
    this.stateService.reset();
    this.localStorageService.removeItem('sw-context-token');
  }
}
