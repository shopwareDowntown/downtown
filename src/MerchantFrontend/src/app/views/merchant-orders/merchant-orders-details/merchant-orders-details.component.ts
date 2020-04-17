import { Component, OnInit } from '@angular/core';
import { Observable, of } from 'rxjs';
import { Order } from '../../../core/models/order.model';
import { ActivatedRoute, Params } from '@angular/router';
import { switchMap } from 'rxjs/operators';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import { ToastService } from '../../../core/services/toast.service';
import { TranslateService } from '@ngx-translate/core';

export const enum OrderStatus {
  open = 'open',
  paid = 'paid',
  completed = 'completed'
}

@Component({
  selector: 'portal-merchant-orders-details',
  templateUrl: './merchant-orders-details.component.html',
  styleUrls: ['./merchant-orders-details.component.scss']
})
export class MerchantOrdersDetailsComponent implements OnInit {

  constructor(
    private readonly activeRoute: ActivatedRoute,
    private readonly merchantApiService: MerchantApiService,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) { }

  order: Order;
  orderStatus:  OrderStatus;

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
      this.updateOrderStatus();
    })
  }

  markOrderAsCompleted() {
    this.merchantApiService.setOrderCompleted(this.order.id).pipe(
      switchMap(() => {
        return this.merchantApiService.getOrder(this.order.id);
      })
    ).subscribe((order: Order) => {
      this.order = order;
      this.toastService.success(
        this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.COMPLETE_ORDER_SUCCESS_HEADLINE')
      );
      this.updateOrderStatus();
    },
      () => this.toastService.error(
        this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.COMPLETE_ORDER_ERROR_HEADLINE')
      )
    );
  }

  markOrderAsPaid() {
    if (false === confirm(this.translateService.instant('MERCHANT.ORDER.DETAILS.CONFIRM_PAID'))) {
      return;
    }
    this.merchantApiService.setOrderPaid(this.order.id).pipe(
      switchMap(() => {
        return this.merchantApiService.getOrder(this.order.id);
      })
    ).subscribe((updatedOrder: Order) => {
      this.order.stateMachineState = updatedOrder.stateMachineState;
      this.order.transactions = updatedOrder.transactions;
      this.updateOrderStatus();
      this.toastService.success(
        this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.MARK_ORDER_AS_PAID_SUCCESS_HEADLINE')
      );
    }, () => {
      this.toastService.error(
        this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.MARK_ORDER_AS_PAID_ERROR_HEADLINE')
      )
    });
  }

  private updateOrderStatus(): void {
    if (
      this.order.stateMachineState.technicalName === 'open'
      && this.order.transactions[this.order.transactions.length -1].stateMachineState.technicalName === 'open'
    ) {
      this.orderStatus = OrderStatus.open;
      return;
    }
    if (
      this.order.stateMachineState.technicalName === 'open'
      && this.order.transactions[this.order.transactions.length -1].stateMachineState.technicalName === 'paid'
    ) {
      this.orderStatus = OrderStatus.paid;
      return;
    }
    if (
      this.order.stateMachineState.technicalName === 'completed'
    ) {
      this.orderStatus = OrderStatus.completed;
      return;
    }
  }
}
