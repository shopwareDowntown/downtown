import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
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

    return this.stateService.isLoggedIn()
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
