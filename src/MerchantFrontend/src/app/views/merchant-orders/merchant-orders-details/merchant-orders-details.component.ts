import { Component, OnInit } from '@angular/core';
import { Observable, of } from 'rxjs';
import { Order } from '../../../core/models/order.model';
import { ActivatedRoute, Params } from '@angular/router';
import { switchMap } from 'rxjs/operators';
import { MerchantApiService } from '../../../core/services/merchant-api.service';

@Component({
  selector: 'portal-merchant-orders-details',
  templateUrl: './merchant-orders-details.component.html',
  styleUrls: ['./merchant-orders-details.component.scss']
})
export class MerchantOrdersDetailsComponent implements OnInit {

  constructor(
    private readonly activeRoute: ActivatedRoute,
    private readonly merchantApiService: MerchantApiService
  ) { }

  orderId: string;
  order: Order;

  ngOnInit(): void {
    // todo: workaround for missing getOrder API route, refactor when route is existing!
    this.activeRoute.params.pipe(
      switchMap((value: Params) => {
        if (!value.id) {
          return of(false);
        }
        this.orderId = value.id;
        return this.merchantApiService.getOrders();
      })
    ).subscribe((result: boolean | Order[]) => {
      if (result instanceof Boolean) {
        return;
      }
      this.order = (result as Order[]).find((order: Order) => order.id === this.orderId);
    })
  }

}
