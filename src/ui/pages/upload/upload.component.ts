import { Component, ViewChild, ElementRef, } from '@angular/core';
import { NgFor } from '@angular/common';
import { RouterLink } from '@angular/router';
import { HttpEventType } from '@angular/common/http';
import { DndDirective } from '../../directives';
import { ProgressComponent } from '../../components';
import { ApiClient } from '../../services';
import { FileProgress, FileProgressState } from '../../models';

@Component({
  selector: 'app-upload',
  standalone: true,
  imports: [NgFor, RouterLink, DndDirective, ProgressComponent],
  templateUrl: './upload.component.html',
  styleUrl: './upload.component.css'
})

export class UploadComponent {
  @ViewChild("fileDropRef", { static: false }) fileDropEl!: ElementRef;
  files: FileProgress[] = [];
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) 
  {
    this.apiClient = apiClient;
  }

  /**
   * on file drop handler
   */
  onFileDropped($event: Array<any>) {
    this.prepareFilesList($event);
  }

  /**
   * handle file from browsing
   */
  fileBrowseHandler(eventTarget: EventTarget|null) {
    var inputElement = eventTarget as HTMLInputElement;
    if(inputElement?.files == null)
      return;

    this.prepareFilesList(Array.from(inputElement.files));
  }

  /**
   * Delete file from files list
   * @param index (File index)
   */
  deleteFile(index: number) {
    let fileInfo = this.files[index];
    if (fileInfo.state == FileProgressState.Transfering) {
      console.log("Upload in progress.");
      return;
    }
    this.files.splice(index, 1);
  }


  /**
   * Simulate the upload process
   */
  uploadFiles(index: number) {
    if(index >= this.files.length || this.files[index].progress != 0)
      return;

    let fileProgress = this.files[index];
    fileProgress.state = FileProgressState.Transfering;
    this.apiClient.uploadFile(fileProgress.file)
      .subscribe(event => 
        {
          switch(event.type)
          {
            case HttpEventType.UploadProgress: 
              // This is an upload progress event. Compute and show the % done
              fileProgress.progress = Math.round(100 * event.loaded / (event?.total ?? 0));
              break;
            case HttpEventType.ResponseHeader:
              if(!event.ok)
              {
                fileProgress.progress = 100;
                fileProgress.state = FileProgressState.Error;
              }
              break;
            case HttpEventType.Response:
              fileProgress.progress = 100;
              fileProgress.state = FileProgressState.Transfered;
              this.uploadFiles(index++);
              break;
            default:
              break;
          }
        }
      );
  }

  /**
   * Convert Files list to normal array list
   * @param files (Files List)
   */
  prepareFilesList(files: Array<File>) {
    if(files.length == 0)
      return;

    let startIndex = this.files.length;
    for (const file of files) {
      this.files.push(new FileProgress(file));
    }

    this.fileDropEl.nativeElement.value = "";
    this.uploadFiles(startIndex);
  }

    /**
   * format bytes
   * @param bytes (File size in bytes)
   * @param decimals (Decimals point)
   */
    formatBytes(bytes: number, decimals = 2) {
      if (bytes === 0) {
        return "0 Bytes";
      }
      const k = 1024;
      const dm = decimals <= 0 ? 0 : decimals;
      const sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
    }
}
