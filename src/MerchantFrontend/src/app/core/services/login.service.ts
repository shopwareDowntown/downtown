import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { switchMap, tap } from 'rxjs/operators';
import { Merchant, MerchantLoginResult } from '../models/merchant.model';
import { StateService } from '../state/state.service';
import { MerchantApiService } from './merchant-api.service';
import { LocalStorageService } from './local-storage.service';
import { Authority } from '../models/authority.model';

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

  login(username: string, password: string, authority: Authority): Observable<Merchant> {
    return this.merchantApiService.login(username, password, authority.accessKey)
      .pipe(
        tap((result: MerchantLoginResult) => {
          this.stateService.setSwContextToken(result['sw-context-token']);
          this.localStorageService.setItem('sw-context-token', result['sw-context-token']);
          this.stateService.setAuthority(authority);
        }),
        switchMap((result: MerchantLoginResult) => {
          return this.merchantApiService.getMerchant();
        }),
        tap((merchant: Merchant) => {
          this.stateService.setMerchant(merchant);
        }),
      );
  }

  loginWithToken(token: string): Observable<Merchant> {
    this.stateService.setSwContextToken(token);
    return this.merchantApiService.getMerchant()
      .pipe(
        tap((merchant: Merchant) => {
          this.stateService.setMerchant(merchant);
        })
      );
  }

  logout() {
    this.stateService.reset();
    this.localStorageService.removeItem('sw-context-token');
  }
}
