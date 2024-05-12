import { Routes } from '@angular/router';
import { FileEntriesComponent, LogoutComponent, LoginComponent, UploadComponent } from '../pages'
import { LoginActivate } from '../services'

export const routes: Routes = [
    {path: '', component: FileEntriesComponent, canActivate:[LoginActivate]},
    {path: '/', component: FileEntriesComponent, canActivate:[LoginActivate]},
    {path: 'upload', component: UploadComponent, canActivate: [LoginActivate]},
    {path: 'logout', component: LogoutComponent, canActivate:[LoginActivate]},
    {path: 'login', component: LoginComponent},
];
