import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { StateService } from '../state/state.service';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  constructor(private router: Router, private readonly stateService: StateService) {
  }

  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    const area = next.root.firstChild.routeConfig.path;
    let isLoggedInInState$: Observable<boolean>;
    if (area === 'merchant') {
      isLoggedInInState$ = this.stateService.isLoggedInAsMerchant();
    } else if (area === 'organization') {
      isLoggedInInState$ = this.stateService.isLoggedInAsOrganization();
    } else {
      isLoggedInInState$ = of(false);
    }

    return isLoggedInInState$
      .pipe(
        map((isLoggedIn: boolean) => {
          if (!isLoggedIn) {
            return this.router.createUrlTree(['']);
          }
          return isLoggedIn;
        })
      );
  }
}
