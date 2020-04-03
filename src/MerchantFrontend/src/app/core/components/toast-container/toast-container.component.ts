import { OnInit, Component } from '@angular/core';
import { Toast, ToastEvent, ToastEventType } from '../../models/toast.model';
import { ToastService } from '../../services/toast.service';

@Component({
  selector: 'portal-toast-container',
  templateUrl: './toast-container.component.html',
  styleUrls: ['./toast-container.component.scss']
})
export class ToastContainerComponent implements OnInit {
  toasts: Toast[] = [];

  constructor(private toastService: ToastService) {}

  ngOnInit(): void {
    this.toastService.events$.subscribe((event: ToastEvent) => {
      switch (event.type) {
        case ToastEventType.ADD: {
          this.add(event.value);
          break;
        }
        case ToastEventType.CLEAR: {
          this.clear(event.value);
          break;
        }
        case ToastEventType.CLEAR_PERMANENT: {
          this.clearPermanent();
          break;
        }
        default: {
          throw new Error(
            'Could not find event.value in defined events' + event.value
          );
        }
      }
    });
  }

  closeToast(toast: Toast): void {
    this.clear(toast.id);
  }

  add(toast: Toast): void {
    this.toasts.push(toast);
    if (toast.timeout) {
      this.setTimeOut(toast);
    }
  }

  clear(id: number): void {
    if (id) {
      this.toasts.forEach((value: Toast, key: number) => {
        if (value.id === id) {
          this.toasts.splice(key, 1);
        }
      });
    } else {
      throw new Error('could not find Toast to close');
    }
  }

  clearPermanent(): void {
    this.toasts = this.toasts.filter(
      (toast: Toast) => undefined !== toast.timeout
    );
  }

  private setTimeOut(toast: Toast): void {
    setTimeout(() => {
      this.clear(toast.id);
    }, toast.timeout);
  }
}
