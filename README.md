# Hawksearch PHP Integration

This code repository contains PHP code to help a developer to understand and provide the foundation to start the process of integrate Hawksearch's HTML Object and Hybrid Proxy approach to a given project.  

*Disclaimer: Please note that the code here should not be used as production-ready code nor as-is without further testing and/or customization to match the specific branding and utility of your application.  At any time, Hawksearch's APIs and integration may have changed and further customization may be needed in order to get these examples to work.*

Hawksearch is a SaaS search engine build on .NET and Lucene.  They provide custom search engine solutions with advanced machine learning and pattern recognition for e-retailers and businesses.  More information can be found at https://www.hawksearch.com

## Getting Started

To begin, you must have an account with Hawksearch.  Once an account is established, you will be provided an engine name, key, and server environment (e.g. dev / test / api).  This may be provided to you via a URL that will look similar as follows:

```
http://{environment}.hawksearch.net/sites/{engine name}/
```

You will add these pieces of data to `config-sample.php` and rename the file to `config.php`.  Once you do that, you can bind an internal URL, in this case, `http://test.local` to the project root directory and be able to test out each approach by going in your browser:

HTML Object Integration Method:
`http://test.local/htmlobj/`

Hybrid Proxy Integration Method:
`http://test.local/hybridproxy/`



### Prerequisites

- This code was developed in PHP 7.2 but should be backwards compatible to PHP 5.4.  
- cURL must be enabled in your `PHP.ini` (e.g. `http://test.local/hybridproxy/?debug=api `).

### Debug

Because the Hybrid Proxy is a more complex integration, I've also added a debug feature to the code.  At any given time, you can append a querystring parameter of `debug=api` to get the JSON output of data prior to reaching the view.


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
