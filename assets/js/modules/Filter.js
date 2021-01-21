/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 * @property {number} page
 */
export default class Filter{

    /**
     * 
     * @param {HTMLElement|null} element 
     */
    constructor (element){
        if (element === null) {
            return
        }

        this.pagination = element.querySelector('.js-filter-pagination')
        this.content = element.querySelector('.js-filter-content')
        this.sorting = element.querySelector('.js-filter-sorting')
        this.form = element.querySelector('.js-filter-form')
        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1)
        // this.moreNav = this.page === 1
        this.bindEvents()
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents(){
        const aClickListener = e => {
          if (e.target.tagName === "A") {
            e.preventDefault();
            this.loadUrl(e.target.getAttribute("href"));
            }
        };
        this.sorting.addEventListener('click', e => {
            aClickListener(e)
            this.page = 1
        })
        // if (this.moreNav) {
        //     this.pagination.innerHTML = '<button class="btn btn-primary">Voir plus</button>'
        //     this.pagination.querySelector('button').addEventListener('click', this.loadMore.bind(this))
        // }else{
            this.pagination.addEventListener("click", aClickListener);
        // };
        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('keyup', this.loadForm.bind(this))
        })
    }

    // async loadMore(){
    //     const button = this.pagination.querySelector('button')  
    //     button.setAttribute('disabled','disabled') 
    //     this.page++
    //     const url = new URL(window.location.href)
    //     const params = new URLSearchParams(url.search)
    //     params.set('page', this.page)
    //     await this.loadUrl(url.pathname + '?' + params.toString(), true)
    //     button.removeAttribute('disabled')
    // }

    async loadForm(){
        this.page = 1
        const data = new FormData(this.form)
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        const params = new URLSearchParams()
        data.forEach((value,key) => {
            params.append(key,value)
        })
        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl(url, append = false){
        this.showLoader();
        const params = new URLSearchParams(url.split('?')[1] || '')
        params.set('ajax',1)
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With':'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            this.content.innerHTML = data.content
            this.sorting.innerHTML = data.sorting
            if (!this.moreNav) {   
                this.pagination.innerHTML = data.pagination
            }else if(this.page === data.pages){
                this.pagination.style.display = 'none';
            }else{
                this.pagination.style.display = null;
            }
            history.replaceState({},'', url.split('?')[0] + '?' + params.toString())
        }else{
            console.error(response)
        }
        this.hideLoader()
    }

    showLoader(){
        this.form.classList.add('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden','false')
        loader.getElementsByClassName.display = null
    }

    hideLoader(){
        this.form.classList.remove("is-loading");
        const loader = this.form.querySelector(".js-loading");
        if (loader === null) {
          return;
        }
        loader.setAttribute("aria-hidden", "true")
        loader.getElementsByClassName.display = 'none';
    }

    
}