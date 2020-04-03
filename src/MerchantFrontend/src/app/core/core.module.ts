import { APP_INITIALIZER, NgModule } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { LayoutsModule } from './layouts/layouts.module';
import { MerchantApiService } from './services/merchant-api.service';
import { HttpClientModule } from '@angular/common/http';
import { LoginService } from './services/login.service';
import { onAppInit } from './app-init';
import { LocalStorageService } from './services/local-storage.service';
import { ToastContainerModule } from './components/toast-container/toast-container.module';
import { ToastModule } from './components/toast/toast.module';

@NgModule({
  imports: [
    CommonModule,
    LayoutsModule,
    HttpClientModule,
    ToastModule,
    ToastContainerModule
  ],
  declarations: [],
  providers: [
    MerchantApiService,
    {
      provide: APP_INITIALIZER,
      useFactory: onAppInit,
      multi: true,
      deps: [Location, LoginService, LocalStorageService]
    }
  ],
  exports: [
    LayoutsModule,
    ToastModule,
    ToastContainerModule
  ]
})
export class CoreModule {}
