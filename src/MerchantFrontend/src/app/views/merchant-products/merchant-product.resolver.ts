import {Injectable} from '@angular/core';
import {ActivatedRouteSnapshot, Resolve, RouterStateSnapshot} from '@angular/router';
import {from, Observable} from 'rxjs';
import {Product} from '../../core/models/product.model';
import {MerchantApiService} from '../../core/services/merchant-api.service';

@Injectable()
export class MerchantProductResolver implements Resolve<Product> {

  constructor(private merchantService: MerchantApiService) {
  }

  resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<Product> {

    if (!route.params.id) {
      return new Observable(subscriber => subscriber.error('No product id provided!'));
    }

    if (!isNaN(route.params.id)) {
      return from(this.merchantService.getProduct(Number(route.params.id)));
    } else {
      return new Observable(subscriber => subscriber.error('Product id is not a number!'));
    }
  }

}
