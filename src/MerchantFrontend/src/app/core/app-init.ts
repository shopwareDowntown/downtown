import { Location } from '@angular/common';
import { catchError } from 'rxjs/operators';
import { LoginService } from './services/login.service';
import { LocalStorageService } from './services/local-storage.service';
import { of } from 'rxjs';

export function onAppInit(locationAngular: Location, loginService: LoginService, localStorageService: LocalStorageService): () => Promise<any> {
  return (): Promise<any> => {
    return new Promise((resolve, reject) => {

      // check for a valid token
      const token = localStorageService.getItem('sw-context-token');
      if (token) {
        loginService.loginWithToken(token)
          .pipe(
            catchError((error: any) => {
              loginService.logout();
              locationAngular.go('login/merchant');
              return of(false);
            })
          )
          .subscribe(() => {
            resolve();
          }, () => {
            resolve();
          });
      } else {
        resolve();
      }
    });
  };
}
