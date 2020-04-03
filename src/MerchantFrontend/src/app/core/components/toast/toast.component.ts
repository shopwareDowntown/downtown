import { Toast } from "../../models/toast.model";
import {
  Component,
  Input,
  AfterViewInit,
  Output,
  EventEmitter,
  ElementRef,
  Renderer2
} from "@angular/core";

@Component({
  selector: "portal-toast",
  templateUrl: "./toast.component.html",
  styleUrls: ["./toast.component.scss"]
})
export class ToastComponent implements AfterViewInit {
  @Input() toast: Toast;
  @Output() closeToast = new EventEmitter();

  constructor(private elementRef: ElementRef, private renderer: Renderer2) {}

  ngAfterViewInit(): void {
    setTimeout(() => {
      window.requestAnimationFrame(() => {
        this.renderer.addClass(this.elementRef.nativeElement, "is--active");
      });
    }, 20);
  }

  close(): void {
    this.closeToast.emit(this.toast);
  }
}
