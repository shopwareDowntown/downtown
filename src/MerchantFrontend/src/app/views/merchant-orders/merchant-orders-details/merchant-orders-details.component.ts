import { Component, OnInit } from '@angular/core';
import { Observable, of } from 'rxjs';
import { Order } from '../../../core/models/order.model';
import { ActivatedRoute, Params } from '@angular/router';
import { switchMap } from 'rxjs/operators';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import { ToastService } from '../../../core/services/toast.service';

@Component({
  selector: 'portal-merchant-orders-details',
  templateUrl: './merchant-orders-details.component.html',
  styleUrls: ['./merchant-orders-details.component.scss']
})
export class MerchantOrdersDetailsComponent implements OnInit {

  constructor(
    private readonly activeRoute: ActivatedRoute,
    private readonly merchantApiService: MerchantApiService,
    private readonly toastService: ToastService
  ) { }

  order: Order;

  ngOnInit(): void {
    this.activeRoute.params.pipe(
      switchMap((value: Params) => {
        if (!value.id) {
          return of(false);
        }
        return this.merchantApiService.getOrder(value.id);
      })
    ).subscribe((result: boolean | Order) => {
      if (result instanceof Boolean) {
        return;
      }
      this.order = (result as Order);
    })
  }

  markOrderAsCompleted() {
    this.merchantApiService.setOrderCompleted(this.order.id).pipe(
      switchMap(() => {
        return this.merchantApiService.getOrder(this.order.id);
      })
    ).subscribe((order: Order) => {
      this.order = order;
      this.toastService.success('Die Bestellung wurde als erledigt markiert.');
    },
      () => this.toastService.error('Die Bestellung konnte nicht als erledigt markiert werden.')
    );
  }
}
