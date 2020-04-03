import { Injectable, OnDestroy } from '@angular/core';
import { Subject, Observable } from 'rxjs';
import { NavigationEnd, Router } from '@angular/router';
import { filter } from 'rxjs/operators';
import { Subscription } from 'rxjs';
import {
  ToastEvent,
  Toast,
  ToastEventType,
  ToastType
} from '../models/toast.model';

@Injectable({
  providedIn: 'root'
})
export class ToastService implements OnDestroy {
  private counter = 0;
  private eventSource: Subject<ToastEvent> = new Subject<ToastEvent>();
  private clearOnNavigationSubscription$: Subscription;
  public events$: Observable<ToastEvent> = this.eventSource.asObservable();

  private readonly defaultTimeOut = 4000;

  constructor(private router: Router) {}

  private add(newToast: Toast): void {
    this.counter++;
    const toast: Toast = {
      id: this.counter,
      title: newToast.title,
      message: newToast.message,
      type: newToast.type,
      timeout: newToast.timeout
    };
    this.emitEvent({
      type: ToastEventType.ADD,
      value: toast
    });
  }

  clearToast(id: number): void {
    this.emitEvent({
      type: ToastEventType.CLEAR,
      value: id
    });
  }

  clearPermanentToasts(): void {
    this.emitEvent({
      type: ToastEventType.CLEAR_PERMANENT
    });
  }

  success(title: string, message?: string): void {
    this.add({
      type: ToastType.SUCCESS,
      title: title,
      message: message,
      timeout: this.defaultTimeOut
    });
  }

  error(title: string, message?: string): void {
    this.add({
      type: ToastType.ERROR,
      title: title,
      message: message
    });
  }

  info(title: string, message?: string): void {
    this.add({
      type: ToastType.INFO,
      title: title,
      message: message
    });
  }

  enableClearOnNavigation(): void {
    if (this.clearOnNavigationSubscription$) {
      return;
    }
    this.clearOnNavigationSubscription$ = this.router.events
      .pipe(filter(event => event instanceof NavigationEnd))
      .subscribe(() => {
        this.clearPermanentToasts();
      });
  }

  private emitEvent(event: ToastEvent): void {
    if (this.eventSource) {
      this.eventSource.next(event);
    }
  }

  ngOnDestroy() {
    if (this.clearOnNavigationSubscription$) {
      this.clearOnNavigationSubscription$.unsubscribe();
    }
  }
}
