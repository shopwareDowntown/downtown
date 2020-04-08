import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Merchant, MerchantRegistration, MerchantLoginResult, PasswordReset } from '../models/merchant.model';
import { Product, ProductListData } from '../models/product.model';
import { StateService } from '../state/state.service';
import { map, take } from 'rxjs/operators';
import { Category } from '../models/category.model';
import { Country } from '../models/country.model';
import { environment } from '../../../environments/environment';
import { Order, OrderListData } from '../models/order.model';
import { Organization, OrganizationAuthority, OrganizationLoginResult } from '../models/organization.model';
import { Voucher, VoucherListData } from '../models/voucher.model';

@Injectable({
  providedIn: 'root'
})
export class MerchantApiService {

  private accessKey = environment.accessKey;
  private apiUrl = environment.apiUrl;

  constructor(
    private readonly http: HttpClient,
    private stateService: StateService
  ) {
  }

  // merchant routes

  loginMerchant(username: string, password: string): Observable<MerchantLoginResult> {
    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');

    const body = JSON.stringify(
      {
        email: username,
        password: password,
      }
    );
    return this.http.post<MerchantLoginResult>(this.apiUrl + '/merchant-api/v1/login', body, {headers: headers});
  }

  registerMerchant(merchantRegistration: MerchantRegistration): Observable<any> {
    let headers = new HttpHeaders();
    headers = headers.set('content-type', 'application/json');
    return this.http.post<any>(this.apiUrl + '/merchant-api/v1/register', JSON.stringify(merchantRegistration), {headers: headers});
  }

  getMerchant(): Observable<Merchant> {
    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('sw-context-token', this.getSwContextToken());

    return this.http.get<any>(this.apiUrl + '/merchant-api/v1/profile', {headers: headers})
      .pipe(
        map((merchantData: any) => {
          return {
            id: merchantData.id as string,
            publicCompanyName: merchantData.publicCompanyName || '',
            publicOwner: merchantData.publicOwner || '',
            publicPhoneNumber: merchantData.publicPhoneNumber as string || '',
            publicEmail: merchantData.publicEmail as string || '',
            publicWebsite: merchantData.publicWebsite || '',
            categoryId: merchantData.categoryId || '',
            publicOpeningTimes: merchantData.publicOpeningTimes || '',
            publicDescription: merchantData.publicDescription || '',
            pictures: ['?'],
            public: merchantData.public,
            firstName: merchantData.firstName || '',
            lastName: merchantData.lastName || '',
            street: merchantData.street || '',
            zip: merchantData.zip || '',
            city: merchantData.city || '',
            countryId: merchantData.countryId || '',
            email: merchantData.email as string,
            password: merchantData.password,
            media: merchantData.media,
            cover: merchantData.cover
          } as Merchant;
        })
      );
  }

  updateMerchant(merchant: Merchant): Observable<Merchant> {
    return this.http.patch<Merchant>(this.apiUrl + '/merchant-api/v1/profile', JSON.stringify(merchant), { headers: this.getJsonContentTypeHeaders() });
  }

  addCoverToMerchant(image: File[]): Observable<any> {
    let headers = new HttpHeaders();
    headers = headers.set('sw-context-token', this.getSwContextToken());
    const formData = new FormData();
    formData.append('cover', image[0]);
    return this.http.post<Merchant>(this.apiUrl + '/merchant-api/v1/profile/media', formData, {headers: headers})
  }

  deleteMerchantCoverImage(id: string): Observable<[]> {
    return this.http.delete<[]>(this.apiUrl + '/merchant-api/v1/profile/media/' + id, {headers: this.getHeaders()})
  }

  // category routes

  getCategories(): Observable<Category[]> {
    return this.http.get<Category[]>(this.apiUrl + '/merchant-api/vi/industries', {headers: this.getHeaders()});
  }

  // product routes

  getProducts(limit: number, offset: number): Observable<ProductListData> {
    let params = new HttpParams();
    params = params.append('limit', limit.toString());
    params = params.append('offset', offset.toString());
    return this.http.get<ProductListData>(this.apiUrl + '/merchant-api/v1/products', {
      headers: this.getHeaders(),
      params: params
    });
  }

  getProduct(productId: string): Observable<{ data: Product }> {
    return this.http.get<{ data: Product }>(this.apiUrl + '/merchant-api/v1/products/' + productId.toLowerCase(), {headers: this.getJsonContentTypeHeaders()});
  }

  addProduct(product: Product): Observable<{ data: Product }> {
    return this.http.post<{ data: Product }>(this.apiUrl + '/merchant-api/v1/products', JSON.stringify(product), {headers: this.getJsonContentTypeHeaders()});
  }

  updateProduct(product: Product): Observable<Product> {
    return this.http.post<Product>(this.apiUrl + '/merchant-api/v1/products/' + product.id, JSON.stringify(product), {headers: this.getJsonContentTypeHeaders()});
  }

  deleteProduct(product: Product): Observable<void> {
    return this.http.delete<void>(this.apiUrl + '/merchant-api/products/' + product.id, {headers: this.getJsonContentTypeHeaders()});
  }

  getVouchers(limit: number, offset: number): Observable<VoucherListData> {
    let params = new HttpParams();
    params = params.append('limit', limit.toString());
    params = params.append('offset', offset.toString());
    return this.http.get<VoucherListData>(this.apiUrl + '/merchant-api/v1/voucher-funding/sold/vouchers', {
      params: params,
      headers: this.getJsonContentTypeHeaders()
    });
  }

  addImageToProduct(image: File, productId: string): Observable<any> {
    let headers = new HttpHeaders();
    headers = headers.set('sw-context-token', this.getSwContextToken());
    const formData = new FormData();
    formData.append('media[]', image[0]);
    return this.http.post<any>(this.apiUrl + '/merchant-api/v1/products/' + productId, formData, {headers: headers});
  }

  // authority route

  getAuthorities(): Observable<OrganizationAuthority[]> {
    return this.http.get<OrganizationAuthority[]>(this.apiUrl + '/merchant-api/v1/authorities');
  }

  getCountries(): Observable<{ data: Country[]}> {
    return this.http.get<{ data: Country[]}>(this.apiUrl + '/merchant-api/v1/country', {headers: this.getJsonContentTypeHeaders() });
  }

  resetPassword(email: PasswordReset): Observable<void> {
    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    return this.http.post<any>(this.apiUrl + '/merchant-api/v1/reset-password', JSON.stringify(email), {headers: headers})
  }

  resetPasswordConfirm(password: string, token: string): Observable<void> {
    const body = {
      newPassword: password,
      token: token
    };

    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');

    return this.http.post<any>(this.apiUrl + '/merchant-api/v1/reset-password-confirm', JSON.stringify(body), {headers: headers})
  }

  getOrders(limit: number, offset:number): Observable<OrderListData> {
    let params = new HttpParams();
    params = params.append('limit', limit.toString());
    params = params.append('offset', offset.toString());
    return this.http.get<OrderListData>(this.apiUrl + '/merchant-api/v1/orders', {
      headers: this.getJsonContentTypeHeaders(),
      params: params
    });
  }

  getOrder(id: string): Observable<Order> {
    return this.http.get<Order>(this.apiUrl + '/merchant-api/v1/order/' + id, {headers: this.getJsonContentTypeHeaders()})
  }

  setOrderCompleted(id: string): Observable<Order> {
    return this.http.patch<Order>(this.apiUrl + '/merchant-api/v1/order/' + id +'/done', null, {headers: this.getJsonContentTypeHeaders()});
  }

  redeemVoucher(voucher: Voucher): Observable<{data: string}> {
    return this.http.post<{data: string}>(this.apiUrl + '/merchant-api/v1/voucher-funding/voucher/redeem', JSON.stringify({ code: voucher.code}), {headers: this.getJsonContentTypeHeaders()});
  }

  //organization routes

  loginOrganization(username: string, password: string): Observable<OrganizationLoginResult> {
    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');

    const body = JSON.stringify(
      {
        email: username,
        password: password,
      }
    );
    return this.http.post<OrganizationLoginResult>(this.apiUrl + '/organization-api/v1/login', body, {headers: headers});
  }

  getOrganization(): Observable<Organization> {
    return this.http.get<Organization>(this.apiUrl + '/organization-api/v1/organization', {headers: this.getJsonContentTypeHeaders()});
  }

  private getHeaders(): { [header: string]: string | string[];} {
    return {
      'sw-context-token': this.getSwContextToken()
    };
  }

  private getJsonContentTypeHeaders(): HttpHeaders {
    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('sw-context-token', this.getSwContextToken());
    return headers;
  }

  private getSwContextToken(): string {
    let token: string;
    this.stateService.getSwContextToken()
      .pipe(
        take(1)
      )
      .subscribe(t => token = t);

    return token ? token : '';
  }
}
