import {Component, OnDestroy, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {Product} from '../../../core/models/product.model';
import {Subscription} from 'rxjs';

@Component({
  selector: 'portal-merchant-products-detail',
  templateUrl: './merchant-products-detail.component.html',
  styleUrls: ['./merchant-products-detail.component.scss']
})
export class MerchantProductsDetailComponent implements OnInit, OnDestroy {

  product: Product;

  // Subscription
  private subResolver: Subscription;

  constructor(private activeRoute: ActivatedRoute) {
    // Get resolved product from route
    this.subResolver = this.activeRoute.data.subscribe(value => {
      this.product = value.product;
    });
  }

  ngOnInit(): void {
  }

  ngOnDestroy(): void {
    if (this.subResolver) {
      this.subResolver.unsubscribe();
    }
  }

}
