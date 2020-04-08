import { Injectable } from '@angular/core';

import { Merchant } from '../models/merchant.model';
import { BehaviorSubject, combineLatest, Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { Organization } from '../models/organization.model';

export const enum Role {
  merchant = 'merchant',
  organization = 'organization'
}

@Injectable({
  providedIn: 'root'
})
export class StateService {

  private _organization = new BehaviorSubject<Organization | null>(null);
  private _merchant = new BehaviorSubject<Merchant | null>(null);
  private _swContextToken = new BehaviorSubject<string | null>(null);

  getOrganization(): Observable<Organization | null> {
    return this._organization;
  }

  setOrganization(value: Organization | null) {
    this._organization.next(value);
  }

  getLoggedInRole():Observable<Role|null> {
    return combineLatest([
      this.getMerchant(),
      this.getOrganization()
    ]).pipe(
      map(([merchant, organization]: [Merchant, Organization]) => {
        if (merchant) {
          return Role.merchant;
        }
        if (organization) {
          return Role.organization;
        }
        return null;
      })
    )
  }

  getMerchant(): Observable<Merchant | null> {
    return this._merchant;
  }

  setMerchant(value: Merchant | null): void {
    this._merchant.next(value);
  }

  getSwContextToken(): Observable<string | null> {
    return this._swContextToken;
  }

  setSwContextToken(value: string | null): void {
    this._swContextToken.next(value);
  }

  isLoggedInAsMerchant(): Observable<boolean> {
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

  isLoggedInAsOrganization(): Observable<boolean> {
    return combineLatest([
      this.getSwContextToken(),
      this.getOrganization()
    ]).pipe (
      map(([swContextToken, authority]: [string | null, Organization | null]) => {
        return swContextToken && null !== authority;
      })
    )
  }

  reset(): void {
    this.setOrganization(null);
    this.setMerchant(null);
    this.setSwContextToken(null);
  }
}
