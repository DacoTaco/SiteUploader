export enum FileProgressState
{
    Ready = 0,
    Transfering,
    Transfered,
    Error,
}

export class FileProgress {
    file: File;
    progress: number = 0;
    state: FileProgressState = FileProgressState.Ready;

    constructor(file: File){ this.file = file; }
}