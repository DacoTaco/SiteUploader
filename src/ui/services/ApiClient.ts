import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders, HttpRequest, HttpResponse, HttpEventType, HttpEvent } from '@angular/common/http';
import { FileEntry, LoginResponse } from '../models'
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class ApiClient 
{
  private baseUrl: string = ".";

  constructor(private httpClient: HttpClient, private router: Router) { }

  loginUser(username: string, password: string): Observable<LoginResponse>
  {
    var header = {
      headers: new HttpHeaders()
        .set('Authorization',  `Basic ${btoa(`${username}:${password}`)}`)
    }
    return this.httpClient.get<LoginResponse>(`${this.baseUrl}/api/?route=login`, header);
  }

  getFiles() : Observable<FileEntry[]>
  {
    return this.httpClient.get<FileEntry[]>(`${this.baseUrl}/api/?route=files`);
  }

  uploadFile(file: File): Observable<HttpEvent<FileEntry>>
  {
    const formData = new FormData();
    formData.append('filedata', file);

    const req = new HttpRequest('POST', `${this.baseUrl}/api/?route=files`, formData, {
      reportProgress: true,
    });

    return this.httpClient.request<FileEntry>(req);
  }
}