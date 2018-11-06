# Jekyll configuration

If you want to edit this documentation and test it locally, you'll need to run Jekyll locally.

## Installation

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

Open your browser and go to `http://localhost:4000`. Jekyll will detect changes automatically and will rebuild MD files as soon as you save them.
