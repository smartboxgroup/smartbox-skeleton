# Jekyll configuration

If you want to edit this documentation and test it locally, you'll need to run Jekyll locally.

## Installation

### Ubuntu

First install all the ruby dependencies:

```bash
sudo apt-get install ruby ruby-dev build-essential
```

Then install Bundler

```bash
sudo gem install bundler
```

Now go to the docs folder and install all the dependencies

```bash
bundle install
```

And now just run the Jekyll server

```bash
bundle exec jekyll serve
```

### Docker

First access to the /docs folder inside the project
```bash
cd docs/
```

Then bring up the docker container with Jekyll server
```bash
docker-compose up
```

### For both

Open your browser and go to `http://localhost:4000/smartesb-skeleton/`. Jekyll will detect changes automatically and will rebuild MD files as soon as you save them.

## Styles

Regarding the style that we are using for the GitHub Pages, we are using [Twitter Bootstrap](https://getbootstrap.com/) as the front end framework.

If you want to change the style for the code blocks, you can use [rougify](https://github.com/jneen/rouge). Here are some examples:
```bash 

rougify style monokai > assets/css/syntax.css
rougify style github > assets/css/syntax.css
rougify style thankful_eyes > assets/css/syntax.css
rougify style pastie > assets/css/syntax.css
rougify style tulip > assets/css/syntax.css
rougify style gruvbox > assets/css/syntax.css
rougify style colorful > assets/css/syntax.css
```

The themes can be found on the [Rouge GitHub Page](https://github.com/jneen/rouge/tree/master/lib/rouge/themes)
The one that we are currently using is the GitHub theme. 