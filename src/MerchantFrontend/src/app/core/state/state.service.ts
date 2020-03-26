import { Injectable } from '@angular/core';
import { Authority } from '../models/authority.model';
import { Merchant } from '../models/merchant.model';
import { BehaviorSubject, combineLatest, Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class StateService {

  private _authority = new BehaviorSubject<Authority | null>(null);
  private _merchant = new BehaviorSubject<Merchant | null>(null);
  private _swContextToken = new BehaviorSubject<string | null>(null);

  getAuthority(): Observable<Authority | null> {
    return this._authority;
  }

  setAuthority(value: Authority | null) {
    this._authority.next(value);
  }

  getMerchant(): Observable<Merchant | null> {
    return this._merchant;
  }

  setMerchant(value: Merchant | null) {
    this._merchant.next(value);
  }

  getSwContextToken(): Observable<string | null> {
    return this._swContextToken;
  }

  setSwContextToken(value: string | null) {
    this._swContextToken.next(value);
  }

  isLoggedIn(): Observable<boolean> {
    return combineLatest([
      this.getSwContextToken(),
      this.getMerchant()
    ])
      .pipe(
        map(([swContextToken, merchant]: [string | null, Merchant | null]) => {
          return swContextToken && null !== merchant;
        })
      );
  }

  reset() {
    this.setAuthority(null);
    this.setMerchant(null);
    this.setSwContextToken(null);
  }
}
