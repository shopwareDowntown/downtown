import { Component, OnInit } from '@angular/core';
import { MerchantApiService } from '../../../core/services/merchant-api.service';
import { Order, OrderListData } from '../../../core/models/order.model';
import { Router } from '@angular/router';
import { switchMap } from 'rxjs/operators';
import { ToastService } from '../../../core/services/toast.service';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'portal-merchant-orders-listing',
  templateUrl: './merchant-orders-listing.component.html',
  styleUrls: ['./merchant-orders-listing.component.scss']
})
export class MerchantOrdersListingComponent implements OnInit {

  loading: boolean;
  orders: any;
  limit = 10;
  offset: number;
  currentPage = 1;
  total: number;
  fromOrder: number;
  tillOrder: number;

  constructor(
    private readonly merchantApiService: MerchantApiService,
    private readonly router: Router,
    private readonly toastService: ToastService,
    private readonly translateService: TranslateService
  ) { }

  ngOnInit(): void {
    this.offset = 0;
     this.refresh();
  }

  refresh() {
    this.loading = true;
    this.pageChange();
    this.merchantApiService.getOrders(this.limit, this.offset).subscribe((orderList: OrderListData) => {
      this.orders = orderList.data;
      this.total = orderList.total;
      this.paginationChange();
      this.loading = false;
    });
  }

  openDetails(order: Order) {
    this.router.navigate(['/merchant/orders/details/' + order.id]);
  }

  markOrderAsCompleted(order: Order) {
    this.merchantApiService.setOrderCompleted(order.id).pipe(
      switchMap(() => {
        return this.merchantApiService.getOrder(order.id);
      })
    ).subscribe((updatedOrder: Order) => {
        order.stateMachineState = updatedOrder.stateMachineState;
        this.toastService.success(
          this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.COMPLETE_ORDER_SUCCESS_HEADLINE')
        );
      },
      () => this.toastService.error(
        this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.COMPLETE_ORDER_ERROR_HEADLINE')
      )
    );
  }

  markOrderAsPaid(order: Order) {
    if (false === confirm(this.translateService.instant('MERCHANT.ORDER.DETAILS.CONFIRM_PAID'))) {
      return;
    }
    this.merchantApiService.setOrderPaid(order.id).pipe(
      switchMap(() => {
        return this.merchantApiService.getOrder(order.id)
      })
    ).subscribe((updatedOrder: Order) => {
      order.stateMachineState = updatedOrder.stateMachineState;
      order.transactions = updatedOrder.transactions;
      this.toastService.success(
        this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.MARK_ORDER_AS_PAID_SUCCESS_HEADLINE')
      )
    }, () => {
      this.toastService.error(
        this.translateService.instant('MERCHANT.ORDER.TOAST_MESSAGES.MARK_ORDER_AS_PAID_ERROR_HEADLINE')
      )
    });
  }

  pageChange(): void {
    this.offset = (this.currentPage - 1) * 10;
  }

  paginationChange(): void {
    if (this.currentPage === 1) {
      this.fromOrder = this.fromOrder = 1;
      if (this.total === 0) {
        this.fromOrder = 0;
      }
    } else {
      this.fromOrder = (this.currentPage -1) * this.limit;
    }

    if (this.fromOrder + this.limit <= this.total) {
      this.tillOrder = this.fromOrder + this.limit;
      if (this.fromOrder === 1) {
        this.tillOrder -= 1;
      }
    } else {
      this.tillOrder = this.total;
    }
  }
}
