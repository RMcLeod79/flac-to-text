# Flac To Text

This is a simple endpoint where you can send speech in flac format, it will then use the Google Speech API to get a transcription.

## Endpoints
`/api/file/upload`

This endpoint expects a POST request with json as the body, the json should contain two pieces of information. The filename without a file extension and the base64 encoded contents of the file.

```
{
    "filename": "audiofile",
    "content": "VGhpcyBpcyBqdXN0IGFuIGV4YW1wbGU="
}
```

The endpoint will return a 203 response on success, indicating that it has accepted the file and will start processing it, it will also return the id of the transcription so that you can retrieve it.

In case of an error a 400 response will be returned along with a description of the error.

```
{
    "error_message": "File is not a valid flac file"
}
```

`/api/transcription/{id}`
A GET request to this endpoint replacing {id} with the id of the transcription, will on success return a 200 response code and a JSON representation of the transcription.

```
{
	"id": 1,
	"filename": "test2022-04-27 11:33:49.flac",
	"transcription": "The transcribed text",
	"submitted": "2022-04-27 11:33:49",
	"status": "Complete",
	"error": null,
	"created_at": "2022-04-27T11:33:49.000000Z",
	"updated_at": "2022-04-27T11:33:52.000000Z"
}
```
Or it will return a 404 response if the id is not found.
